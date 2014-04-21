package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;
import org.w3c.dom.Text;

import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.text.Editable;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class ReplyToClaim extends Activity {

	
	public String msg ;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_reply_to_claim);
		
		
	
		final EditText reptxt = (EditText)findViewById(R.id.txtreply);
		final EditText subj = (EditText)findViewById(R.id.Subject);
		final EditText recv = (EditText)findViewById(R.id.receiver);
		
		Button repbtn = (Button)findViewById(R.id.btnreply);
		
		//values passed by the claim activity
		String subjectM = null ;
		String receiverM = null;
		
		subj.setText(subjectM);
		recv.setText(receiverM);
	
	
	
	
	repbtn.setOnClickListener(new View.OnClickListener() {
		
		@Override
		public void onClick(View v) 
		{
			
			msg = reptxt.getText().toString() ;
			
				
			
			
		}
	});
	
	 
    JSONObject json = new JSONObject();
    try {
        json.put("responce_content" ,msg );
        json.put("username",recv );
    } catch (JSONException e) {
        e.printStackTrace();
    }

   
    PostRequest request = new PostRequest("http://entangle.io/user/login/{username,password}/{claimID}"){
        protected void onPostExecute(String response) {  
             if( this.getStatusCode() == 201 ){
                 viewSuccessMessage(response);
              }else if( this.getStatusCode() == 400 ) {
                  showErrorMessage();
              }
         }

		private void showErrorMessage() {
			String errormsg = "Error , message not sent";
			TextView stsMsg = (TextView)findViewById(R.id.statusmsg) ;
			stsMsg.setText(errormsg);
			
			
		}

		private void viewSuccessMessage(String response) {
			String successmsg = "message has been sent";
			TextView stsMsg = (TextView)findViewById(R.id.statusmsg) ;
			stsMsg.setText(successmsg);
			
		}
    };
    
    
    request.setBody(json); 
    request.addHeader("X", "Hi"); 
    request.execute(); 
	
	
	
	
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.reply_to_claim, menu);
		return true;
	}

}
