pipeline {
    agent any

    options {
        timestamps()
    }

    environment {
        HEALTH_URL = 'http://host.docker.internal:8080/health'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Preparar entorno') {
            steps {
                sh '''
                    if [ ! -f .env ]; then
                        cp .env.example .env
                    fi

                    echo "Archivo .env disponible para Docker Compose"
                '''
            }
        }

        stage('Validación PHP') {
            steps {
                sh '''
                    docker run --rm \
                        -v "$PWD":/app \
                        -w /app \
                        php:8.2-cli \
                        sh -c 'find . -path ./.git -prune -o -name "*.php" -print0 | xargs -0 -n1 php -l'
                '''
            }
        }

        stage('Docker Build') {
            steps {
                sh '''
                    docker compose build
                '''
            }
        }

        stage('Docker Deploy') {
            steps {
                sh '''
                    docker compose down --remove-orphans || true
                    docker compose up -d
                '''
            }
        }

        stage('Health Check') {
            steps {
                sh '''
                    echo "Esperando disponibilidad de la API..."

                    for i in $(seq 1 30); do
                        RESPONSE=$(curl -fsS "$HEALTH_URL" || true)

                        echo "$RESPONSE"

                        echo "$RESPONSE" | grep -q '"status": "success"' && \
                        echo "$RESPONSE" | grep -q '"database": "connected"' && \
                        exit 0

                        echo "Intento $i/30: API aún no disponible"
                        sleep 2
                    done

                    echo "La API no respondió correctamente al health check"
                    exit 1
                '''
            }
        }

        stage('Resultado') {
            steps {
                echo 'Pipeline ejecutado correctamente. API y base de datos funcionando.'
            }
        }
    }

    post {
        success {
            echo 'SUCCESS: Integración continua finalizada correctamente.'
        }

        failure {
            echo 'FAILED: El pipeline falló. Mostrando estado de contenedores...'
            sh 'docker compose ps || true'
            sh 'docker compose logs api --tail=80 || true'
            sh 'docker compose logs db --tail=80 || true'
        }
    }
}