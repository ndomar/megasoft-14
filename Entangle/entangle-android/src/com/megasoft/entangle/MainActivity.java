package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;
import android.widget.Toast;

import com.megasoft.requests.DeleteRequest;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		DeleteRequest test = new DeleteRequest("http://requestb.in/1c6vons1"){
			protected void onPostExecute(String response) {
		        if( !this.hasError() ){
		            test("success");
		        }else{
		        	test(this.getErrorMessage());
		        }
		    }
		};
		
		test.addHeader("X", "Hi");
		test.execute();
		
	}
	public void test(String test){
		Toast.makeText(this, test, Toast.LENGTH_LONG).show();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true; 
	}

}
