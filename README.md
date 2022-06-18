## Setup

- create a service account and download your credentials in JSON format by using [this guide](https://developers.google.com/workspace/guides/create-credentials)
- rename the downloaded JSON file to 'google_client_credentials.json' and place it in the docker subdirectory
- create a new spreadsheet in Google Sheets or share an existing one with the email of your service account

## Input files
- OPTIONAL: overwrite the catalog_sample.xml with your own as it'll be copied to the container so you can use it
- you can specify a direct URL to an XML file instead of a local path

## Actions

#### Build the Docker image first
```
$ make build
```

### Run tests (with coverage)
```
$ make test
```

### Run the CLI command
```
$ make dev
```

You must provide two parameters when the CLI command starts:

- path to a local file or a direct link to remote file (you can use the 'catalog_sample.xml' as it'll be copied to the root of the project)
- ID of the existing spreadsheet you have previously shared with your service account
- the spreadsheet will be cleared before the new data will be inserted
- for this exercise, we will assume that the default sheet (Sheet1) exists and we will only be updating that sheet

### Run shell
```
$ make bash
```

### Stop and remove container(s)
```
$ make stop
```
