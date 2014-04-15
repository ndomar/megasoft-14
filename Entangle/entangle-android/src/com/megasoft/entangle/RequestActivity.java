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
import android.widget.CheckBox;
import android.widget.EditText;
import com.megasoft.requests.PostRequest;




/* intent is required to be redirected to this activity with sessionId and tangleId
*/


public class RequestActivity extends Activity{
	    Button Post;
	    EditText description;
	    EditText requestedPrice;
	    EditText date;
	    EditText deadLine;
	    EditText tags;
        CheckBox checkBox;
        int requiredFields = 0;
        boolean flag;
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
	/*	Intent previousIntent = getIntent();
		final int tangleID = previousIntent.getIntExtra("tangleID" , 0);
		final String sessionId = previousIntent.getStringExtra("sessionId"); */
		setContentView(R.layout.activity_request);
		description = (EditText) findViewById(R.id.description);
		requestedPrice = (EditText) findViewById(R.id.price);
		date = (EditText) findViewById(R.id.date);
		deadLine = (EditText) findViewById(R.id.deadLine);
	    tags = (EditText) findViewById(R.id.tags);
	    Post = (Button) findViewById(R.id.post);
	    checkBox = (CheckBox) findViewById(R.id.checkBox);
		final JSONObject json = new JSONObject();
		Post.setEnabled(false);
		description.setOnFocusChangeListener(focusListener);
		requestedPrice.setOnFocusChangeListener(focusListener);
		date.setOnFocusChangeListener(focusListener);
		deadLine.setOnFocusChangeListener(focusListener);
		tags.setOnFocusChangeListener(focusListener);
		
		

       /* Post.setOnClickListener(new View.OnClickListener() {
			
			
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
		}); */
      
	}
		
	OnFocusChangeListener focusListener = new OnFocusChangeListener() {
		
		
		public void onFocusChange(View view, boolean hasFocus) {
			EditText editText = (EditText) view;
		    if(!hasFocus){
		    	if(isEmpty(editText)) {
				Post.setEnabled(false);
				requiredFields --;
			} else{
				requiredFields ++;
			}
		} else {
		    if(!flag){
				flag = true;
				checkBox.setChecked(false);
			}
		}
		}
	};
	private boolean isEmpty(EditText editText){
		if( editText.getText().toString().length() == 0 ){
		    editText.setError("This Field is Required");
		    return true;
		}
		editText.setError(null);
		return false;
	}
	private void enablePostButton(){
		if(description.getError() == null && requestedPrice.getError() == null
				&& date.getError() == null && deadLine.getError() == null
				&& tags.getError() == null && requiredFields >= 5 && checkBox.isChecked()) {
			Post.setEnabled(true);
		}
	}
	public void itemClicked(View v) {
    	View focusedView = getCurrentFocus();
    	focusedView.clearFocus();
        CheckBox checkBox = (CheckBox)v;
        if(checkBox.isChecked()){
        	flag = false;
        	enablePostButton();
        }
    }
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		//getMenuInflater().inflate(R.menu.requests, menu);
		return true;
	}
	
	



}
