[![Open in Visual Studio Code](https://open.vscode.dev/badges/open-in-vscode.svg)](https://open.vscode.dev/SpeedyLom/WhenDoYouFinish) ![php workflow results](https://github.com/SpeedyLom/WhenDoYouFinish/actions/workflows/php.yml/badge.svg)

# WhenDoYouFinish

A single page app that uses [Toggl](https://toggl.com) to display an estimated finishing time based on fixed working
hours written in [PHP](https://php.net).

![A large title "When do you finish?" followed by a panel with a grey heading and border displaying the day and date (Monday 8th). In the body of the panel an estimated finishing time (3.58pm) and the time worked in hours and minutes (7 hours and 16 minutes) below. At the bottom of the panel is a progress bar showing the amount of the day worked in a percentage (96%).](resources/WhenDoYouFinish_social.png "WhenDoYouFinish Screenshot")

If you are working remotely and frequently get the following questions throughout your working day:

> When do you finish?
> How much longer do you have left?
> What time do you finish?

Then **WhenDoYouFinish** will allow you to divert that question leaving you to carry on with your work uninterrupted.
Simply direct your spouse/child/friend/goldfish at your **WhenDoYouFinish** page and let them find out for themselves.

## How it works?

**WhenDoYouFinish** uses the entries (recorded and running) from your Toggl account to provide a total amount of time
worked, this time worked is compared against a fixed working day length to provide an _estimated finish_.

## What do you need?

In order to be able to run your own **WhenDoYouFinish** page you'll need a:

- [Free Toggl account](https://toggl.com/plan/pricing) (or paid)
- Machine running PHP `8.0` (or later)
- Fixed workday length (i.e. 7.5 hours)
- Knowledge of running commands from a terminal

# Getting started

To get started on running your own **WhenDoYouFinish** page check you can meet the requirements below and then follow
the installation steps.

## Requirements

The technical requirements for this project are:

* [PHP 8.0+](https://www.php.net/releases/8.0/en.php)
* [composer](https://getcomposer.org/)
* [curl](https://curl.se/)
* [JSON](http://www.json.org/json-en.html)

## Installation

Providing your machine has been set up to meet the technical requirements, the first step is
to [clone this repository](https://docs.github.com/en/get-started/getting-started-with-git/about-remote-repositories#choosing-a-url-for-your-remote-repository)
to your machine that will run your page.

```bash
git clone https://github.com/SpeedyLom/WhenDoYouFinish.git
```

Once the project has been cloned then use `composer` to install any dependencies:

```bash
cd WhenDoYouFinish
composer install --no-dev
```

### Configuration

After installing any dependencies copy the `configuration.example.json`
to `configuration.json`
and set your:

* [Toggl API token](https://support.toggl.com/en/articles/3116844-where-is-my-api-key-located)
* Environment (`production`)
* User agent (your email address)
* Workday length in minutes ([default 7.5 hours](https://duckduckgo.com/?q=450+minutes+to+hours&t=ffab&ia=answer))
* Toggl workspace ID (numbers proceeding `/settings/general` after
  following [this URL](https://track.toggl.com/settings/))

```json
{
  "api_token": "123456789ABCDEFG123456789ABCDEFG",
  "environment": "production",
  "user_agent": "your_username@example.com",
  "workday_length_in_minutes": 450,
  "workspace_id": 1234567
}
```

### Running

With the configuration in place you can start the application making the page available to friends and loved ones.

#### PHP Built-in Server

One of the simplest ways to start your **WhenDoYouFinish** instance is to
use [PHP's Built-in web server](https://www.php.net/manual/en/features.commandline.webserver.php) which can be done from
the root of your `WhenDoYouFinish/` directory.

```bash
php -S 0.0.0.0:8080
```

## Usage

With your instance up and running you can then access the page on port `8080` at your machine's local IP address
i.e. http://192.168.0.42:8080.

If you are unsure of your machine's IP address you can use a command like `hostname` to find it out:

```bash
hostname -I
```
