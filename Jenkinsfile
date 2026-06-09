pipeline {
    agent any

    options {
        timestamps()
        skipDefaultCheckout(true)
    }

    environment {
        HEALTH_URL = 'http://host.docker.internal:8080/health'
        COMPOSE_FILE_CI = 'docker-compose.ci.yml'
        COMPOSE_PROJECT = 'shortener_ci'
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
                        sh -lc 'find . -path "./.git" -prune -o -name "*.php" -print | while read file; do php -l "$file"; done'
                '''
            }
        }

        stage('Docker Build') {
            steps {
                sh '''
                    docker compose -p "$COMPOSE_PROJECT" -f "$COMPOSE_FILE_CI" build --no-cache
                '''
            }
        }

        stage('Docker Deploy') {
            steps {
                sh '''
                    docker compose -p "$COMPOSE_PROJECT" -f "$COMPOSE_FILE_CI" down -v || true
                    docker compose -p "$COMPOSE_PROJECT" -f "$COMPOSE_FILE_CI" up -d
                    docker compose -p "$COMPOSE_PROJECT" -f "$COMPOSE_FILE_CI" ps
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
            sh 'docker compose -p "$COMPOSE_PROJECT" -f "$COMPOSE_FILE_CI" ps || true'
            sh 'docker compose -p "$COMPOSE_PROJECT" -f "$COMPOSE_FILE_CI" logs api --tail=80 || true'
            sh 'docker compose -p "$COMPOSE_PROJECT" -f "$COMPOSE_FILE_CI" logs db --tail=80 || true'
        }
    }
}