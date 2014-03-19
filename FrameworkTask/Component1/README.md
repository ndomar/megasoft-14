# Address Book Notes

### How to use request classes

```java
new postRequest(){
 	protected void onPostExecute(String response) {
		if( !this.hasError() )){
			doSomething(response);
		}else{
			showErrorMessage();
		}
	}
}.execute("URL PATH HERE" , json.toString() );
```
The JSON returned is in the form of string , use JSONObject class or JSONArray class to parse this string. The same goes for the get and delete requests but they don't have the second argument in the execute method which is the body.

### How to run symfony
* Make sure that the machine is running ( vagrant up ).
* access http://10.11.12.13/addressbook/web/app_dev.php/

### Notes
* GET and DELETE methods don't have a body.
