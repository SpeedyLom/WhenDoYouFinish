[![Open in Visual Studio Code](https://open.vscode.dev/badges/open-in-vscode.svg)](https://open.vscode.dev/SpeedyLom/WhenDoYouFinish)
# WhenDoYouFinish
Use PHP and Toggl to allow others to track your workday and put a stop to the question "When do you finish?".

# Installation
Clone the repository to your favorite place on your machine that has PHP.

## Requirements
* PHP 7.3+
* curl

## Environment
Copy the `.example.env` to `.env` update with the following:
* Toggl API token
* User agent
* Toggl workspace ID

```dotenv
API_TOKEN=123465789A123465789B123465789CDE
USER_AGENT=youremailaddress@example.com
WORKDAY_LENGTH_IN_MINUTES=450
WORKSPACE_ID=1234567
```

# Running
# PHP Built-in Server
From the directory that you cloned the repository into use PHP's built-in server to run the application.
```bash
php -S 0.0.0.0:8080
```
# Use
Send your loved one to your machine's IP on port `8080` e.g. `http://192.168.0.10:8080`.

# FAQs
## Why is the amount of time shown incorrect?
Toggl will only record time into your workspace once it is complete. For an up-to-date value restart the current item you are tracking, creating a new entry and ending the previous one.