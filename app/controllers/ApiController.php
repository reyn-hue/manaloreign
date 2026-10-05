<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('UserModel');
        $this->call->model('ProductModel');
        $this->call->library('auth');
    }

    public function signup()
    {
        $input = $this->input();
        $username = trim((string) ($input['username'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if ($username === '' || strlen($username) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $this->response->send_json_error('Enter a valid username and email, and a password with at least 8 characters.', 422);
            return;
        }

        if ($this->UserModel->findByEmail($email)) {
            $this->response->send_json_error('An account with that email already exists.', 409);
            return;
        }

        $created = $this->UserModel->create([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
        ]);

        if (!$created) {
            $this->response->send_json_error('Unable to create the account.', 500);
            return;
        }

        $this->response->send_json_success(null, 'Account created. You can now log in.', 201);
    }

    public function login()
    {
        $input = $this->input();
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $user = $email !== '' ? $this->UserModel->findByEmail($email) : null;

        if (!$user || empty($user['is_active']) || !password_verify($password, $user['password'])) {
            $this->response->send_json_error('Invalid email or password.', 401);
            return;
        }

        $this->auth->login($user['id']);
        $this->response->send_json_success([
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
        ], 'Logged in successfully.');
    }

    public function session()
    {
        $userId = $this->auth->user_id();
        $user = $userId ? $this->UserModel->findById($userId) : null;

        if (!$user || empty($user['is_active'])) {
            $this->response->send_json_error('Not authenticated.', 401);
            return;
        }

        $this->response->send_json_success([
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
        ]);
    }

    public function logout()
    {
        $this->auth->logout();
        $this->response->send_json_success(null, 'Logged out.');
    }

    public function products()
    {
        if (!$this->requireAuthentication()) {
            return;
        }

        $this->response->send_json_success($this->ProductModel->all());
    }

    public function store_product()
    {
        if (!$this->requireAuthentication()) {
            return;
        }

        $product = $this->validatedProduct($this->input());
        if ($product === null) {
            return;
        }

        $id = $this->ProductModel->insert($product);
        if ($id === false) {
            $this->response->send_json_error('Unable to add product.', 422);
            return;
        }

        $this->response->send_json_success(['id' => $id], 'Product added successfully.', 201);
    }

    public function update_product($id)
    {
        if (!$this->requireAuthentication()) {
            return;
        }

        if (!ctype_digit((string) $id) || (int) $id < 1) {
            $this->response->send_json_error('Invalid product ID.', 400);
            return;
        }

        $product = $this->validatedProduct($this->input());
        if ($product === null) {
            return;
        }

        $updated = $this->ProductModel->update((int) $id, $product);
        if ($updated === false) {
            $this->response->send_json_error('Unable to update product.', 422);
            return;
        }

        $this->response->send_json_success(null, 'Product updated successfully.');
    }

    public function delete_product($id)
    {
        if (!$this->requireAuthentication()) {
            return;
        }

        if (!ctype_digit((string) $id) || (int) $id < 1) {
            $this->response->send_json_error('Invalid product ID.', 400);
            return;
        }

        $deleted = $this->ProductModel->delete((int) $id);
        if (!$deleted) {
            $this->response->send_json_error('Product not found.', 404);
            return;
        }

        $this->response->send_json_success(null, 'Product deleted successfully.');
    }

    private function input()
    {
        $json = $this->request->json();
        return is_array($json) ? $json : $this->request->post();
    }

    private function requireAuthentication()
    {
        if (!$this->auth->user_id()) {
            $this->response->send_json_error('Please log in to continue.', 401);
            return false;
        }

        return true;
    }

    private function validatedProduct(array $input)
    {
        $name = trim((string) ($input['product_name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $price = $input['price'] ?? null;
        $quantity = $input['quantity'] ?? null;

        if ($name === '' || strlen($name) > 100 || !is_numeric($price) || (float) $price < 0
            || filter_var($quantity, FILTER_VALIDATE_INT) === false || (int) $quantity < 0) {
            $this->response->send_json_error('Enter a product name, a non-negative price, and a non-negative whole-number quantity.', 422);
            return null;
        }

        return [
            'product_name' => $name,
            'description' => $description,
            'price' => number_format((float) $price, 2, '.', ''),
            'quantity' => (int) $quantity,
        ];
    }
}
