#!/usr/bin/env bash

for i in "$@"
do
case ${i} in
    -t=*|--type=*)
    TYPE="${i#*=}"
    shift # past argument=value
    ;;
    -m=*|--message=*)
    MESSAGE="${i#*=}"
    shift # past argument=value
    ;;
    *)
          # unknown option
    ;;
esac
done

if [[ ${TYPE} == '' ]]; then
    echo "Enter a type major|minor|patch"
    exit
fi

if [[ ${TYPE} != 'major' && ${TYPE} != 'minor' && ${TYPE} != 'patch' ]]; then
    echo "The type should be one of these major|minor|patch"
    exit
fi

if [[ ${MESSAGE} == '' ]]; then
    MESSAGE="Update"
fi

semver inc ${TYPE}
git add .
git commit -m ${MESSAGE}
git push origin master

git tag -a `semver tag` -m ${MESSAGE}
git push --tags
