### A PHP API for AUMS

This is an on official api written in PHP to access the Amrita University Manangement System in a secure manner.

##### Including the library

This library uses [Composer](https://getcomposer.org/) for dependency management and autoloading.

Include this in your ```composer.json``` file


    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/niranjan94/aums-api"
        }
    ],
    "require": {
        "niranjan94/aums-api": "dev-master"
    }


Run ```composer update``` and you're good to go.

##### Usage instructions

Initialize the ```\Aums\API``` class.

    $api = new \Aums\API("full_roll_number", "password");
    
Call the ```login``` method

    /**
     * Start the login flow
     * @throws AumsOfflineException
     * @throws CredentialsInvalidException
     * @throws CredentialsMissingException
     * @return array An array containing basic student info and link to profile pic
     */
    $api->login()
    
    
Do whatever you wanna do with the output ;-)

    // Example output
    Array
    (
        [roll_no] => CB.EN.U4AEE9999
        [first_name] => NIRANJAN
        [last_name] => R
        [email] => niranjan94@yahoo.com
        [phone] => 9622100100
        [degree_program] => B.Tech2012
        [branch] => AE
        [semester] => 7
        [image_filename] => U3TqEMc7FoJey8mlbQu8LLGFwY8owf-I67I2gR41uGU
    )