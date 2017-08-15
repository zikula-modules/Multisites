pipeline {
    agent any 

    options {
        buildDiscarder(logRotator(numToKeepStr: '5'))
    }

    stages {
        stage('Prepare') {
            steps {
                sh 'rm -rf build/api'
                sh 'rm -rf build/coverage'
                sh 'rm -rf build/logs'
                sh 'rm -rf build/pdepend'
                sh 'rm -rf build/phpdox'
                sh 'mkdir build/api'
                sh 'mkdir build/coverage'
                sh 'mkdir build/logs'
                sh 'mkdir build/pdepend'
                sh 'mkdir build/phpdox'
            }
        }
        stage('Composer Install') {
            steps {
                sh 'cd build && wget -nc "http://getcomposer.org/composer.phar" && cd ..'
                sh 'chmod +x build/composer.phar'
                sh 'build/composer.phar install'
            }
        }
        stage('PHP Syntax check') {
            steps {
                sh 'vendor/bin/parallel-lint --exclude vendor/ .'
            }
        }
        stage('Test') {
            steps {
                sh 'vendor/bin/phpunit -c phpunit.xml || exit 0'
                step([
                    $class: 'XUnitBuilder',
                    thresholds: [[$class: 'FailedThreshold', unstableThreshold: '1']],
                    tools: [[$class: 'JUnitType', pattern: 'build/logs/junit.xml']]
                ])
                publishHTML([allowMissing: false, alwaysLinkToLastBuild: false, keepAll: false, reportDir: 'build/coverage', reportFiles: 'index.html', reportName: 'Coverage Report', reportTitles: ''])
                step([$class: 'CloverPublisher', cloverReportDir: 'build/coverage', cloverReportFileName: 'build/logs/clover.xml'])
                /* BROKEN step([$class: 'hudson.plugins.crap4j.Crap4JPublisher', reportPattern: 'build/logs/crap4j.xml', healthThreshold: '10']) */
            }
        }
        stage('Checkstyle') {
            steps {
                sh 'vendor/bin/phpcs --report=checkstyle --report-file=`pwd`/build/logs/checkstyle.xml --standard=PSR2 --extensions=php --ignore=autoload.php --ignore=vendor/ . || exit 0'
                checkstyle pattern: 'build/logs/checkstyle.xml'
            }
        }
        stage('Lines of Code') {
            steps {
                sh 'vendor/bin/phploc --count-tests --exclude vendor/ --log-csv build/logs/phploc.csv --log-xml build/logs/phploc.xml .'
            }
        }
        stage('Copy paste detection') {
            steps {
                sh 'vendor/bin/phpcpd --log-pmd build/logs/pmd-cpd.xml --exclude vendor . || exit 0'
                dry canRunOnFailed: true, pattern: 'build/logs/pmd-cpd.xml'
            }
        }
        stage('Mess detection') {
            steps {
                sh 'vendor/bin/phpmd . xml build/phpmd.xml --reportfile build/logs/pmd.xml --exclude vendor/ || exit 0'
                pmd canRunOnFailed: true, pattern: 'build/logs/pmd.xml'
            }
        }
        stage('Software metrics') {
            steps {
                sh 'vendor/bin/pdepend --jdepend-xml=build/logs/jdepend.xml --jdepend-chart=build/pdepend/dependencies.svg --overview-pyramid=build/pdepend/overview-pyramid.svg --ignore=vendor .'
            }
        }
        stage('Generate documentation') {
            steps {
                sh 'vendor/bin/phpdox -f build/phpdox.xml'
            }
        }
    }
}
