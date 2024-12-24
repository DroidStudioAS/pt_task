# Instructions for Running a Laravel Task

1. **Clone the Project via HTTPS**  
   Use the HTTPS URL to clone the repository to your local environment.

2. **Create a `.env` File**  
   Create a `.env` file based on the `.env.example` file included in the project.

3. **Set Up the Database**
   - Ensure that you have created a database matching the name specified in your `.env` file.
   - Configure the database credentials (username, password) in the `.env` file.
   - **Note:** Also configure the credentials for sending emails in case of a failed data import.

4. **Run Database Migrations**  
   Once the above steps are complete, run the following command to set up the database schema:
   ```bash
   php artisan migrate
   ```

5. **Seed the Database**  
   After the database is ready, populate it with initial data by running:
   ```bash
   php artisan db:seed
   ```
   This will create the initial users and permissions in the database.
   - **Initial Users:**
   - `admin@example.com` with password: `password`
   - `test@example.com` with password: `password`

6. **Start the Queue Worker**  
   Open a new terminal window and start the queue worker to handle asynchronous operations:
   ```bash
   php artisan queue:work
   ```

7. **Start the Server**  
   Launch the development server and test the application with the following command:
   ```bash
   php artisan serve
   ```

That's it! Thank you for your time. Feel free to reach out with any questions.

---

## Additional Notes

- **Populate the Database with Mock Users**  
  If you'd like to add mock users to the database, run:
  ```bash
  php artisan db:seed userseeder
  ```
  This will create 100 users, all with the password: `password`.

