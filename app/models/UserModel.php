<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Model: UserModel
 *
 * Automatically generated via CLI.
 */
class UserModel extends Model {
    protected $table = 'users';
    protected $primary_key = 'id';
    protected $fillable = [];
    protected $guarded = ['id'];

    public function findByEmail($email) {
        return $this->db->table('users')->where('email', $email)->get();
    }

    public function findById($id) {
        return $this->db->table('users')->where('id', $id)->get();
    }

    public function create($data) {
        return $this->db->table('users')->insert($data);
    }
}