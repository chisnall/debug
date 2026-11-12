<?php
if (!function_exists('testFunction')) {
    function testFunction() {
        squiz("This is a test function from helpers.php");
    }
}

if (!function_exists('getProperty')) {
    /**
     * Read a private/protected property (searches parent classes too).
     */
    function getProperty($object, $name)
    {
        $class = new ReflectionClass($object);

        while (!$class->hasProperty($name)) {
            $class = $class->getParentClass();
            if ($class === false) {
                throw new InvalidArgumentException("Property '$name' not found");
            }
        }

        $property = $class->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }

        return $headers;
    }
}
