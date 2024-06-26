<?php

/* Define your own constants at the end of this file */

//TODO load this from dotenv

// BOILER PLATE REQUIRED CONFIG PARAMS //

/**
 * Server Listening Address
 * This is the address OpenSwoole will listen on
 */
const SERVER = [
    'bind' => '127.0.0.1',
    'port' => 5023
];


  /**
  * Enable Disable Debug Mode, set this to false in production
  */

const DEBUG = true;


 /**
  * By default, set to False
  * If set to true :  Request Methods in @see SCHEMA_VALIDATION_RQST_METHODS will be validated,  Requests without an existing json schema in Schemas folder will be dropped
  * If set to false:  If the schema exists, requests will be validated against it, otherwise the request will continue as is.
  * This should be set to true in production to make sure all inputs are sanitized
  * @see \Shahzaib\Framework\Core\Middlewares\SchemaValidator
  */

const FORCE_RQST_SCHEMA = false;


/**
 * This is for autoloading JSON schemas to validate requests data.
 * @see https://opis.io/json-schema/2.x/php-loader.html
 * @see \Shahzaib\Framework\Core\Middlewares\SchemaValidator
 * @see \Shahzaib\Framework\Core\App::registerMiddlewares()
 */
const SCHEMA_PREFIX = 'https://api.example.com';


/**
 * Used to reference the project root
 */
const ROOT = __DIR__;



/**
 * This controls the execution of async events, How often events should be read from table and executed in milliseconds
 * @see https://openswoole.com/docs/modules/swoole-timer
 * @note To support maximum parallel connections without exhausting memory, this timer should be as small as possible >= 2
 */
const ASYNC_TASK_TIMER = 500;


/**
 * This controls the size of Table that holds async events and their data in KBs (until they are processed and deleted)
 * @see https://openswoole.com/docs/modules/swoole-table
 * @note This constant cannot be greater than available memory (RAM)
 *
 */
const ASYNC_TASK_MEMORY_LIMIT = 2000;


/**
 * This controls the size of data stored for an event prior to its processing in number of characters
 * @see https://openswoole.com/docs/modules/swoole-table
 */
const ASYNC_TASK_DATA_SIZE = 5000;


/**
 * Request Methods that are evaluated against JSON Schema
 */
const SCHEMA_VALIDATION_RQST_METHODS = [
    'PUT',
    'POST',
    'PATCH'
];


// PROJECT SPECIFIC CONFIG //


/**
 * Define your database connection here
 * All supported params of Eloquent are supported here
 * set to false/empty array if database is not required
 */
const DB = [
    'driver' => 'mysql',
    'host' => '',
    'database' => '',
    'username' => '',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci',
];


const JWT_KEY = 'c1f8c80a-b2f3-11ed-afa1-0242ac120002';

const JWT_ALGORITHM = 'HS256';

/**
 * This is not required for the boilerplate to function, it is used in Demo content
 * @see \Shahzaib\Framework\EventListeners\LoggerEvents
 * @see \Shahzaib\Framework\Registrations::getContainerDefinitions()
 */
const LOG_PATH = ROOT.'/logs/application.log';