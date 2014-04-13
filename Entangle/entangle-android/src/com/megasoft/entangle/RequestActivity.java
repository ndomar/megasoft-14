package com.megasoft.entangle;
import org.json.JSONException;
import org.json.JSONObject;
import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Menu;
import android.view.View;
import android.view.View.OnFocusChangeListener;
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
		super.onCreate(savedInstanceState);
		Intent previousIntent = getIntent();
		final int tangleID = previousIntent.getIntExtra("tangleID" , 0);
		final String sessionId = previousIntent.getStringExtra("sessionId");
		setContentView(R.layout.activity_request);
		final EditText description = (EditText) findViewById(R.id.description);
		final EditText requestedPrice = (EditText) findViewById(R.id.price);
		final EditText date = (EditText) findViewById(R.id.date);
		final EditText deadLine = (EditText) findViewById(R.id.deadLine);
	    final EditText tags = (EditText) findViewById(R.id.tags);
	    final Button Post = (Button) findViewById(R.id.post);
		final JSONObject json = new JSONObject();
		
		description.setOnFocusChangeListener(new OnFocusChangeListener() {
			
			public void onFocusChange(View v, boolean hasFocus) {
			    if(!hasFocus){
			    	if(isEmpty(description)) {
					Post.setEnabled(false);
				}else {
					Post.setEnabled(true);
					}
				}
			   }
			});
        
		tags.setOnFocusChangeListener(new OnFocusChangeListener() {
			
			public void onFocusChange(View v, boolean hasFocus) {
			    if(!hasFocus){
			    	if(isEmpty(tags)) {
					Post.setEnabled(false);
				}else {
					Post.setEnabled(true);
					}
				}
			   }
			});
        
		date.setOnFocusChangeListener(new OnFocusChangeListener() {
        	 
			
			public void onFocusChange(View v, boolean hasFocus) {
			    if(!hasFocus){
			    	if(isEmpty(date)) {
					Post.setEnabled(false);
				}else {
					Post.setEnabled(true);
					}
				}
			   }
			});
      
		deadLine.setOnFocusChangeListener(new OnFocusChangeListener() {
        	 
			
			public void onFocusChange(View v, boolean hasFocus) {
			    if(!hasFocus){
			    	if(isEmpty(deadLine)) {
					Post.setEnabled(false);
				}else {
					Post.setEnabled(true);
					}
				}
			   }
			});
        requestedPrice.setOnFocusChangeListener(new OnFocusChangeListener() {
        	 
			
			public void onFocusChange(View v, boolean hasFocus) {
			    if(!hasFocus){
			    	if(isEmpty(requestedPrice)) {
					Post.setEnabled(false);
				}else {
					Post.setEnabled(true);
					}
				}
			   }
			});
		

        Post.setOnClickListener(new View.OnClickListener() {
			
			
			public void onClick(View arg0) {
				  try {
			        	
			            json.put("description" , description.getText().toString());
			            json.put("requestedPrice" , requestedPrice.getText().toString());
			            json.put("date" , date.getText().toString());
			            json.put("deadLine" , deadLine.getText().toString());
			            json.put("tags", tags.getText().toString());
			            
			           } catch (JSONException e) {
			            e.printStackTrace();
			           }
				
				
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
	private boolean isEmpty(EditText editText){
		if( editText.getText().toString().length() == 0 ){
		    editText.setError("Enter something I'm giving up on you");
		    return true;
		}
		editText.setError(null);
		return false;
	}
	

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		//getMenuInflater().inflate(R.menu.requests, menu);
		return true;
	}
	
	



}
