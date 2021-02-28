#!/bin/sh

PROJECT=$(git rev-parse --show-toplevel)
STAGED_FILES_CMD=`git diff --cached --name-only --diff-filter=ACMR HEAD | grep \\\\.php$`

echo "Running pre-commit hook"

# Check code by composer libraries (For dev environment)
if [ "$FILES" != "" ]
then
    # PHP Coding Standards Fixer
    if [ -f ./vendor/bin/php-cs-fixer ]
    then
        echo "Starting PHP Coding Standards Fixer"

        for FILE in $STAGED_FILES_CMD
        do
            echo "PHP-CS-FIXER checking file $FILE"
             ./vendor/bin/php-cs-fixer fix --allow-risky=yes $PROJECT/$FILE
        done
    fi

    # Add modified files to git staging
    git add $FILES

    if [ -f ./vendor/bin/psalm ]
    then
        echo "Running PSALM..."
        ./vendor/bin/psalm --show-info=false $FILES

        if [ $? != 0 ]
        then
            echo "Fix the PSALM errors before commit."
            exit 1
        fi
    fi
fi

# Standard PHP Linter. The most important part. Checks syntax errors.
echo "Checking PHP Lint..."
for FILE in $STAGED_FILES_CMD
do
    php -l -d display_errors=1 $PROJECT/$FILE
    if [ $? != 0 ]
    then
        echo "Fix the error before(s) commit."
        exit 1
    fi
    FILES="$FILES $PROJECT/$FILE"
done

exit $?
