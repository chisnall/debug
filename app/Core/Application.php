<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Traits\GetSetProperty;
use App\Exception\FrameworkException;
use Exception;

final class Application
{
    use GetSetProperty;

    private static Application $app;
    private Controller $controller;
    private Logger $logger;
    private Request $request;
    private Response $response;
    private Router $router;
    private Session $session;
    private View $view;
    private array $env = [];
    private array $config;
    private int $errors = 0;

    public function __construct(string $handler = 'framework')
    {
        // Load environment
        $this->loadEnvironment();

        // Sets the exception handler
        set_exception_handler([ExceptionHandler::class, $handler]);

        // Sets the error handler for PHP errors
        set_error_handler([ErrorHandler::class, $handler]);

        // Create instances
        self::$app = $this;
        $this->logger = new Logger();
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session('application');
        $this->view = new View();

        // Init config
        $this->initConfig();

        // Get routes
        includeFile('/app/Routes/routes.php');
    }

    public static function app(): Application
    {
        return self::$app;
    }

    public function controller(): Controller
    {
        return $this->controller;
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function view(): View
    {
        return $this->view;
    }

    private function loadEnvironment()
    {
        $envFile = BASE_PATH . '/.env';

        if (!file_exists($envFile)) {
            throw new Exception("Missing .env file: $envFile");
        }

        $envFile = BASE_PATH . '/.env';

        // Process .env file
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $envLine) {
            // Skip comments
            if (strpos(trim($envLine), '#') === 0) continue;

            // Get key and value and trim
            [$key, $value] = array_map('trim', explode('=', $envLine, 2));

            $this->env[$key] = $value;
        }
    }

    private function initConfig()
    {
        // Check config exists
        $configPath = BASE_PATH . '/config.php';

        if (!file_exists($configPath)) {
            // Using code 10 for this - and need to throw standard Exception
            throw new Exception("Config file is missing:\n$configPath", 10);
        }

        // Get config array
        $configArray = include $configPath;

        // Handle environment variables
        // These will override the config.php values
        foreach (getenv() as $envKey => $envValue) {
            if (strpos($envKey, 'CONFIG_') === 0) {
                $envKeyArray = explode('_', strtolower($envKey));

                // Ignore the first key: CONFIG
                if (count($envKeyArray) == 3) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]] = $envValue;
                }
                elseif (count($envKeyArray) == 4) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]][$envKeyArray[3]] = $envValue;
                }
                elseif (count($envKeyArray) == 5) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]][$envKeyArray[3]][$envKeyArray[4]] = $envValue;
                }
                elseif (count($envKeyArray) == 6) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]][$envKeyArray[3]][$envKeyArray[4]][$envKeyArray[5]] = $envValue;
                }
            }
        }

        // Set config property
        $this->config = $configArray;
    }

    public function run(): void
    {
        // Do not run this in a try/catch block - we want any exceptions from this
        // to be handled by the exceptionHandler()
        echo $this->router->resolve();
    }
}
