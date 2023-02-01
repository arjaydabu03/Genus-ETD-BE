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
// DISPLAY DATA
const USER_DISPLAY = 'User display successfully.';
const CATEGORY_DISPLAY = 'Category display successfully.';
const MATERIAL_DISPLAY = 'Material display successfully.';
const ORDER_DISPLAY = 'Order display successfully.';
//UPDATE
const USER_UPDATE = 'User successfully updated.';
const CATEGORY_UPDATE = 'Category successfully updated.';
const MATERIAL_UPDATE = 'Material successfully updated.';
const ORDER_UPDATE = 'Order successfully updated.';
//SOFT DELETE
const ARCHIVE_STATUS = 'Successfully archived.';
const RESTORE_STATUS = 'Successfully restored.';
//ACCOUNT RESPONSE
const INVALID_RESPONSE = 'The provided credentials are incorrect.';
const CHANGE_PASSWORD = 'Password successfully changed.';
const LOGIN_USER = 'Log-in successfully.';
const LOGOUT_USER = 'Log-out successfully.';

// DISPLAY ERRORS
const NOT_FOUND = 'Data not found.';
const INVALID_CATEGORY = 'Category not found.';
//VALIDATION
const SINGLE_VALIDATION = 'Data has been validated.';
const INVALID_ACTION = 'Invalid action.';

}