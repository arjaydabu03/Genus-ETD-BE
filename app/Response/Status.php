<?php
namespace App\Response;

class Status{
const SUCCESS = 201;
const FAILED = 422;

const CREATE_STATUS = 'Successfully Created';
const EXISTS_STATUS = 'Record Already Exists';
const UPDATE_STATUS = 'Successfully Updated';
const ARCHIVE_STATUS = 'Successfully Deleted';
const RESTORE_STATUS = 'Successfully Restore';


}