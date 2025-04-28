<?php

class ProductsTest
{
    private $baseUrl = 'http://localhost/new_test/api/products.php';

    public function runTests()
    {
        echo "Running testFetchProductsWithValidType...\n";
        $this->testFetchProductsWithValidType();

        echo "Running testFetchProductsWithInvalidType...\n";
        $this->testFetchProductsWithInvalidType();

        echo "Running testFetchProductsWithoutType...\n";
        $this->testFetchProductsWithoutType();

        echo "All tests completed.\n";
    }

    public function testFetchProductsWithValidType()
    {
        $type = 'electronics'; // Replace with a valid type from your database
        $response = $this->makeRequest(['type' => $type]);

        assert(is_array($response), 'Response should be an array');
        assert(!empty($response), 'Response should not be empty');

        foreach ($response as $product) {
            assert(array_key_exists('id', $product), 'Product should have an "id" key');
            assert(array_key_exists('name', $product), 'Product should have a "name" key');
            assert(array_key_exists('description', $product), 'Product should have a "description" key');
            assert(array_key_exists('price', $product), 'Product should have a "price" key');
            assert(array_key_exists('original_price', $product), 'Product should have an "original_price" key');
            assert(array_key_exists('image', $product), 'Product should have an "image" key');
        }
    }

    public function testFetchProductsWithInvalidType()
    {
        $type = 'invalid_type';
        $response = $this->makeRequest(['type' => $type]);

        assert(is_array($response), 'Response should be an array');
        assert(empty($response), 'Response should be empty for an invalid type');
    }

    public function testFetchProductsWithoutType()
    {
        $response = $this->makeRequest();

        assert(is_array($response), 'Response should be an array');
        assert(empty($response), 'Response should be empty when no type is provided');
    }

    private function makeRequest(array $params = [])
    {
        $url = $this->baseUrl . '?' . http_build_query($params);
        $json = file_get_contents($url);
        return json_decode($json, true);
    }
}

// 執行測試
$test = new ProductsTest();
$test->runTests();