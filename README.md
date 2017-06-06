# autoImage
Take an image and automatically create other sizes, caching by saving the results to the folder.

Requesting 400x400_test.jpg will result (if test.jpg exists) in a cropped 400x400 version of test.jpg being returned.

In addition 400x400_test.jpg will be written to the folder to be returned next time the file is requested.

**Note:** *transparency is not retained with PNG and GIF files*

## Parameters
The script can be passed a number of parameters which allow its behaviour to be controlled.

Some of these require a passphrase to be passed to authenticate the request.

### pp=<<passphrase>>
This passphrase needs to match that set in the script.

### cmd=<<command>>
The command you wish to perform. Some are passed alongside the image request and others direct to the script.

**nocache**

Prevents the script from saving the cached copy of the new image on the server.

*Example*
```
/400x400_test.jpg?cmd=nocache
```

**delete**

Call the script directly and delete copies of cached images. 

An extra parameter **fn** needs to be passed to specify the name of the image.

This command requies the passphrase to be passed and correct.

*Example:*
```
/images.php?cmd=delete&fn=test.jpg&pp=secret_passphrase
```

## Configuration options
At the head of the file there are a number of configurable options -

### $allowedSizes
An array containing a list of sizes that can be requested, specified as a string '400x400' etc.

If this is null then any size can be requested.

### $jpegQuality
Affects the quality of saved JPEG files.

### $passPhrase
Pass phrase for accessing administion functions when implemented - this should be changed to something else.
