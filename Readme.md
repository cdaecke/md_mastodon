# TYPO3 Extension `md_mastodon`

With this extension you are able to show Mastodon toots on your TYPO3 website.
It will collect data via the API and display it as a Mastodon social wall.

## Screenshots

Example of Mastodon social wall:
![Screenshot detail](Documentation/Images/mastodon_wall.png?raw=true "Mastodon social wall")

## Requirements

- TYPO3 v11.5

## Installation

- Install the extension by using composer (`composer req mediadreams/md_mastodon`) or the extension manager
- Include the static TypoScript of the extension
- Configure the extension by setting your own Typoscript constants

## Basic configuration

Base configure is possible by setting some constant. This will help you to
skip some configurations for each feed:
- `plugin.tx_mdmastodon_api.settings.apiUrl`<br>
Base API URL, eg. `https://mastodon.social/api/v1/`
- `plugin.tx_mdmastodon_api.settings.apiToken`<br>
The Mastodon API access token

The above two settings can be overwritten in the feed configuration.

- `plugin.tx_mdmastodon_api.settings.includeCss`<br>
Include standard CSS styles for a masonry-layout if the items (toots).

## Usage

### Create a new entry of type `Mastodon configuration`
1. Select module `List`
2. Select a page where to store the record (can be any page)
3. Click the button `Create new record`
4. In the section `Mastodon social networking API` click `Mastodon configuration`

Now you are able to configure a Mastodon feed, which you want to show on the website.

Step 1-3:
![Screenshot detail](Documentation/Images/new_configuration_1.png?raw=true "Step 1-3")

Step 4:
![Screenshot detail](Documentation/Images/new_configuration_2.png?raw=true "Step 4")

**Configure the feed**
- `Title`<br>
This is for internal use only. It is needed, in order to select the feed in the plugin.
- `Base Api URL`<br>
The Mastodon base api URL, like `https://mastodon.social/api/v1/`. You can leave
this empty, if you have configured a default URL in typoscript constant
`plugin.tx_mdmastodon_api.settings.apiUrl`.
- `Api token`<br>
Mastodon API token. You can leave this empty, if you have configured a default
API token in typoscript constant `plugin.tx_mdmastodon_api.settings.apiToken`.
- `Api method`<br>
Select the Mastodon API method.
    - `Accounts`<br>
    Statuses posted to the given account. See <https://docs.joinmastodon.org/methods/accounts/#statuses>
        - `Account ID` (required)<br>
        The ID of the Account in the Mastodon database.
        - `Exclude replies`<br>
        Filter out statuses in reply to a different account.
        - `Exclude reblogs`<br>
        Filter out boosts from the response.
        - `Pinned`<br>
        Filter for pinned statuses only.
    - `Hashtag timeline`<br>
    View public statuses containing the given hashtag. See <https://docs.joinmastodon.org/methods/timelines/#tag>
        - `Hashtag`<br>
        The name of the hashtag (not including the # symbol).
    - `Home timeline`<br>
    View statuses from followed users. See <https://docs.joinmastodon.org/methods/timelines/#home>
    - `List timeline`<br>
    View statuses in the given list timeline. See <https://docs.joinmastodon.org/methods/timelines/#list>
        - `List ID`<br>
        Local Mastodon ID of the list in the database.
    - `Public timeline`<br>
    Public timeline of instance. See <https://docs.joinmastodon.org/methods/timelines/#public>
- `Only media`<br>
Show only statuses with media attached.
- `Update frequency`<br>
Decide how often the feed should be updated (number in seconds).
- `Import date` (read only)<br>
Date of last update.
- `Data` (read only)<br>
JSON response of the API call.

### Create scheduler task
- Select module `Scheduler`
- Click the button `Add task`
- In field `Class` select `Execute console commands`
- Choose a `Frequency`. Note: This can be set for example to 5 minutes and the respective Mastodon feed will be updated, as set in the configuration of the feed (field `Update frequency`).
- In field `Schedulable Command. Save and reopen to define command arguments` choose `mdmastodon:import`
- Save

### Create Plugin to show entries
- Select module `Page`
- Select the page where to show the Mastodon feed
- Create new content element of type `General Plugin`
- Switch to tab `Plugin` and select `Mastodon`
- In field `Mastodon configuration` find the configuration, which you have created in the step before
- Decide, how many entries to show be setting a value in field `Limit`
- Save

**ATTENTION**

Be aware, that the feed needs to be imported by running the scheduler task
before it can be displayed by the plugin!

## Bugs and Known Issues
If you find a bug, it would be nice if you add an issue on [Github](https://github.com/cdaecke/md_mastodon/issues).

# THANKS

Thanks a lot to all who make this outstanding TYPO3 project possible!

**The TYPO3 project - inspiring people to share!**
