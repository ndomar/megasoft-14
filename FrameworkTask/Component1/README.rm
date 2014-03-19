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
The JSON returned is in the form of string , use JSONObject class or JSONArray class to parse this string. The same goes for the get and delete requests but they don't have the second argument in the execute method wich is the body.

### Notes
* GET and DELETE methods don't have a body.
