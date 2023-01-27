<?php
namespace App\Response;

class Status{
const SUCCESS = 201;
const FAILED = 422;
//CRUD OPERATION
const REGISTERED = 'User successfully registered.';
const CATEGORY_SAVE = 'Category successfully registered.';
const MATERIAL_SAVE = 'Material successfully registered.';
const ORDER_SAVE = 'Order successfully saved.';
const USER_DISPLAY = 'User display successfully.';
//UPDATE
const USER_UPDATE = 'User successfully updated.';
const CATEGORY_UPDATE = 'Category successfully updated.';
const MATERIAL_UPDATE = 'Material successfully updated.';
//SOFT DELETE
const ARCHIVE_STATUS = 'Successfully archive.';
const RESTORE_STATUS = 'Successfully restore.';
//ACCOUNT RESPONSE
const INVALID_RESPONSE = 'The provided credentials are incorrect.';
const CHANGE_PASSWORD = 'Password successfully changed.';
const LOGIN_USER = 'Log-in successfully.';
const LOGOUT_USER = 'Log-out successfully.';

// DISPLAY
const NOT_FOUND = 'Data not found.';

}