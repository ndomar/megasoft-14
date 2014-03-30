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




/* intent is required to be redirected to this activity with sessionId and tangleId
*/


public class RequestActivity extends Activity{

	 
    
    /*this method create json object and send it through 
     PostRequest 
     
     */
 	
	protected void onCreate(Bundle savedInstanceState) {
		Intent previousIntent = getIntent();
		final int tangleID = previousIntent.getIntExtra("tangleID" , 0);
		final String sessionId = previousIntent.getStringExtra("sessionId");
		
		EditText description   = (EditText) findViewById(R.id.editText2);
		 EditText requestedPrice   = (EditText) findViewById(R.id.editText5);
		 EditText date   = (EditText) findViewById(R.id.editText1);
		 EditText deadLine   = (EditText) findViewById(R.id.editText4);
	    EditText tags   = (EditText) findViewById(R.id.editText3);
	    Button Post    = (Button) findViewById(R.id.button1);
	  
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_request);
		
		final JSONObject json = new JSONObject();
        try {
        	
            json.put("description" , description );
            json.put("requestedPrice" , requestedPrice );
            json.put("date" , date );
            json.put("deadLine" , deadLine );
            json.put("tags", tags);
            
           
            
        } catch (JSONException e) {
            e.printStackTrace();
        }

        Post.setOnClickListener(new View.OnClickListener() {
			
			
			public void onClick(View arg0) {
				
				 PostRequest request = new PostRequest("http://entangle2.io/tangle/" + tangleID + "/request"){
			            protected void onPostExecute(String response) {  
			                 if( this.getStatusCode() == 201 ){
			                     //redirection
			                  }else if( this.getStatusCode() == 400 ) {
			                     // showErrorMessage();
			                  }
			             }
			        };
			        request.setBody(json); 
					request.addHeader("X-SESSION-ID", sessionId);
			        request.execute();
			      
			}
		});
      
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		//getMenuInflater().inflate(R.menu.requests, menu);
		return true;
	}
	
	



}
