## LARAVEL 8.* | CUSTOMER-PRODUCT IMPORT | ORDER REST API

## Installation steps

 - Download zip or clone the repository
 - Set/Configure database settings in .env file
 - Update package by running composer update/install command -- `composer update`
 - Migrated database -- `php arisan migrate`
 - Import Product and customer from csv  -- `php arisan import:masterdata`
 - Run the project by command -- `php arisan serve`

## API Document

 - Docs: https://docs.google.com/document/d/1BINhivTbD_--tjVcgm0W-mrBD4GrLfpILGuzQB9dgFY/edit?usp=sharing
 - Postman API Docs: https://documenter.getpostman.com/view/346658/2s9Y5TzkFz
 - Postman collection: https://speeding-station-161572.postman.co/workspace/New-Team-Workspace~a1dbccfa-56f8-4b66-97a0-7c9bbdeb4555/collection/346658-05b48cd8-9989-4144-a903-b6a67a6bbf2e?action=share&creator=346658
 
## Estimated and tracked time

 - Project analysis and architecture/models design : 4 Hours 
 - Laravel project setup and create migration for all models : 2 Hours
 - Create Models for Customers, Products, Orders and Order Items with Relationship : 3 Hours
 - Create artisan command to import products and customers with logs : 2 Hours
 - Create REST API for order crud operation : 4 Hours
 - Create REST API for add/remove product from order and calculate net amount : 2 Hours 
 - Create REST API to process order payment and manage repository payment functions : 2 Hours
 - Test all API and create Documentation : 3 Hours 