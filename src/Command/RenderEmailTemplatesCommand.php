<?php

namespace NetBull\FoundationEmailsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Twig\Environment;

/**
 * Class RenderEmailTemplatesCommand
 * @package NetBull\FoundationEmailsBundle\Command
 */
class RenderEmailTemplatesCommand extends Command
{
    private ParameterBagInterface $parameterBag;
    private Environment $twig;
    private array $templates = [];

    /**
     * RenderEmailTemplatesCommand constructor.
     * @param ParameterBagInterface $parameterBag
     * @param Environment $twig
     * @param string|null $name
     */
    public function __construct(ParameterBagInterface $parameterBag, Environment $twig, string $name = null)
    {
        parent::__construct($name);
        $this->parameterBag = $parameterBag;
        $this->twig = $twig;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('netbull:emails:render')
            ->addOption('template', 't', InputOption::VALUE_OPTIONAL, 'Specific template to render');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $templatesPath = $this->parameterBag->get('netbull_foundation_emails.templates_path');
        $finder = new Finder();
        $finder->in($templatesPath)
            ->exclude('Snippets')
            ->name('*.inky.twig')
            ->notName('*layout.inky.twig')
            ->files();

        $this->templates = [];
        foreach ($finder as $file) {
            $this->templates[] = str_replace($projectDir.'/templates/', '', $file->getPathname());
        }
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $chosenTemplate = $input->getOption('template');

        if ($chosenTemplate && !in_array($chosenTemplate, $this->templates)) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the template you want to render',
                $this->templates,
                0
            );
            $question->setErrorMessage('Template %s is invalid.');

            $chosenTemplate = $helper->ask($input, $output, $question);
            $output->writeln('You have just selected: '.$chosenTemplate);
        }

        foreach ($this->templates as $template) {
            if (!$chosenTemplate || $chosenTemplate === $template) {
                try {
                    $html = $this->twig->render($template);

                    // Defines the output directory where rendered templates will be saved
                    $outputDir = $this->parameterBag->get('netbull_foundation_emails.rendered_templates_path');
                    if (!is_dir($outputDir)) {
                        mkdir($outputDir, 0777, true);
                    }

                    // Save the rendered output to a file
                    $outputPath = $outputDir . '/' . basename($template, '.inky.twig') . '.html';
                    file_put_contents($outputPath, $html);

                    $output->writeln(sprintf('Template rendered to %s', $outputPath));
                } catch (\Exception $e) {
                    $output->writeln(sprintf('Error rendering template: %s', $e->getMessage()));
                    return Command::FAILURE;
                }
            }
        }

        return Command::SUCCESS;
    }
}