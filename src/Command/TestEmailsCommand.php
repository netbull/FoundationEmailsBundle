<?php

namespace NetBull\FoundationEmailsBundle\Command;

use NetBull\FoundationEmailsBundle\Event\SendMailEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(name: 'netbull:emails:test')]
class TestEmailsCommand extends Command
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $dispatcher;

    /**
     * @var array
     */
    private array $templates = [];

    /**
     * @param ParameterBagInterface $parameterBag
     * @param EventDispatcherInterface $dispatcher
     * @param string|null $name
     */
    public function __construct(ParameterBagInterface $parameterBag, EventDispatcherInterface $dispatcher, string $name = null)
    {
        parent::__construct($name);
        $this->parameterBag = $parameterBag;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption('template', 't', InputOption::VALUE_OPTIONAL, 'Specific template to test');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $chosenTemplate = $input->getOption('template');

        if ($chosenTemplate && !in_array($chosenTemplate, $this->templates)) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the template which you want to test',
                $this->templates,
                0
            );
            $question->setErrorMessage('Template %s is invalid.');

            $chosenTemplate = $helper->ask($input, $output, $question);
            $output->writeln('You have just selected: '.$chosenTemplate);
        }

        foreach ($this->templates as $template) {
            if (!$chosenTemplate || $chosenTemplate === $template) {
                $event = new SendMailEvent(
                    $template,
                    [],
                    'Test email: '.$template,
                    $this->parameterBag->get('maintenance_email')
                );
                $output->writeln('Sending: '.$template);
                $this->dispatcher->dispatch($event);
            }
        }

        return Command::SUCCESS;
    }
}
