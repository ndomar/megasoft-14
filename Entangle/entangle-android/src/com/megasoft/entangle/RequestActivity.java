package com.megasoft.entangle;
import org.json.JSONException;
import org.json.JSONObject;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import com.megasoft.requests.PostRequest;

public class RequestActivity extends Activity{

	 EditText body   = (EditText) findViewById(R.id.editText2);
	 EditText title   = (EditText) findViewById(R.id.editText1);
    Button Post    = (Button) findViewById(R.id.button1);
    EditText tags   = (EditText) findViewById(R.id.editText3);
    final Activity self = this;

	
	protected void onCreate(Bundle savedInstanceState) {
		Intent intent = getIntent();
		int tangleID = intent.getIntExtra("tangleID" , 0);
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_request);
		
		JSONObject json = new JSONObject();
        try {
            json.put("description" , body );
            json.put("tags", tags);
            json.put("title", title);
           
            
        } catch (JSONException e) {
            e.printStackTrace();
        }

        //Creating a new Post Request
       PostRequest request = new PostRequest("http://entangle2.io/tangle/" + tangleID + "/request"){
            protected void onPostExecute(String response) {  
                 if( this.getStatusCode() == 201 ){
                     //viewSuccessMessage(response);
                  }else if( this.getStatusCode() == 400 ) {
                     // showErrorMessage();
                  }
             }
        };
        request.setBody(json); 
      //  request.addHeader("X", "Hi"); 
        request.execute();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		//getMenuInflater().inflate(R.menu.requests, menu);
		return true;
	}
	
	



}
