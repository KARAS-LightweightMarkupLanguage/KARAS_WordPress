# KARAS_WordPress



## About

KARAS is lightweight markup language. And this is the plugin to use KARAS in WordPress.
If you want to get more info about KARAS, please visit [lightweightmarkuplanguage.com](http://lightweightmarkuplanguage.com).



## Setting

This WordPress plugin is hook to ``` the_content ``` as *Filter*. Unzip download file, and rename the directory to  **KARAS_WordPress**. Then, put the renamed directory into ``` plugins ``` directory. Finally, it is important to change the permission of the plugin directory of KARAS. KARAS's plugin and the parent directory have to be readable(R) and executable(X) from the user. For example, such permission value is '0705'. Be careful, directory of KARAS's plugin is not equal to the directory of WordPress's plugin. 



## How to Use

Meta-box of KARAS (custom fields) will appear in the article post screen. If check, the syntax of the KARAS in your article will be converted.

When KARAS is applied, any other filters hook to ``` prepend_attachment ``` will not be applied. Filter which hooked later than KARAS will be apply. In order to change this behavior, need knowledge about WordPress and the plugin developing. When your WordPress not using finlter plugin, no problem will occur. 



## System requirements

Need PHP version 5.2 or later.



## License

KARAS and the converters are licensed under BSD.
KARAS for WordPress is licensed under BSD.
