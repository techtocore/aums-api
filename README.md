### A PHP API for AUMS

This is an unofficial api written in PHP to access the Amrita University Manangement System in a secure manner.

> This API is as secure as logging in from the AUMS website

##### Including the library

This library uses [Composer](https://getcomposer.org/) for dependency management and autoloading.

Include this in your ```composer.json``` file

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/niranjan94/aums-api"
    }
],
"require": {
    "niranjan94/aums-api": "dev-master"
}
```

Run ```composer update``` and you're good to go.

##### Usage instructions

Initialize the ```\Aums\API``` class.

```php
$api = new \Aums\API("full_roll_number", "password");
```

Call the ```setStorageDir``` method and set the storage directory to a writable directory

```php
/**
 * Set the storage directory to store cookies and image files
 * @param string $storageDir
 * @throws StorageIOException
 */
$api->setStorageDir($path_to_a_writable_dir)
```

Call the ```login``` method

```php
/**
 * Start the login flow
 * @throws AumsOfflineException
 * @throws CredentialsInvalidException
 * @throws CredentialsMissingException
 * @return array An array containing basic student info and link to profile pic
 */
$api->login()
```
    
Do whatever you wanna do with the output ;-)

```php
// Example output
Array
(
    [roll_no] => CB.EN.U4AEE12029
    [first_name] => NIRANJAN
    [last_name] => R
    [email] => niranjan94@yahoo.com
    [phone] => 9600514966
    [degree_program] => B.Tech2012
    [branch] => AE
    [semester] => 7
    [image_filename] => U3TqEMc7FoJey8mlbQu8LLGFwY8owf-I67I2gR41uGU
)
```
