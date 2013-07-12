Country Code Extractor
=================

This is a symphony extension that plugs itself in `DataSourcePostExecute`, that is after a datasource has executed. 
And takes any nodes representing phone numbers and extracts the country code when a valid one is found.

It takes as input any phone number format such as:

356xxxxxxxx
356 xx xxx xxx
(+356) xxxxxxxx
00356xxxxxxxx

and outputs

`<nodename country-code='356'>xxxxxxxx</nodename>`

##Dependency

Currently this extension depends on [Frontend Tracking](https://github.com/jonmifsud/frontend_tracking) only the files are required no need to be installed

##Configuration

As much as I'd love this to be clever and guess any nodes where you have a phone number, it's currently not-so-clever.
So you'll need to modify your Config file manually. (yes I didn't have time to make it look nice in the backend)

The config uses the array key to denote the name of the datasource; and the value represents a comma-separated list of phone numbers to be decoded.

###Example

	###### COPUNTRY CODE EXTRACTOR ######
	'country_code_extractor' => array(
		'user' => 'phone,mobile',
		'{datasource-handle}' => '{xpath1},{xpath2}',
	),
	########

##Note

I actually have **not** tried a complete xpath; but rather a single node; so if this does not work and is required I'll update accordingly.