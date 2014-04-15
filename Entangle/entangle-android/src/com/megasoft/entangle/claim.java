package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.HttpRequest;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

public class claim extends Activity {
	
	String offererMail;
	String tangleOwenerMail;
	String subject;
	String mssgBody;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.claimform);
		offererMail = this.getIntent().getStringExtra("sender");
		tangleOwenerMail = this.getIntent().getStringExtra("reciever");
		EditText fromMail = (EditText) findViewById(R.id.fromText);
		EditText toMail = (EditText) findViewById(R.id.toText);
		fromMail.append(offererMail);
		toMail.append(tangleOwenerMail);
	} 
	
	public void sendClaimForm(View v) { 
		
		final Intent intent = new Intent(this, MainActivity.class);
		subject = ((EditText) findViewById(R.id.subjectText)).getText().toString();
		mssgBody = ((EditText) findViewById(R.id.mssgText)).getText().toString();
		JSONObject object = new JSONObject();
		try {
			object.put("X-SENDERMAIL", offererMail);
			object.put("X-RECIEVERMAIL", tangleOwenerMail);
			object.put("X-SUBJECT", subject);
			object.put("X-MSSGBODY", mssgBody);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		PostRequest postSubject = new PostRequest("api") { //post subj
			
			protected void onPostExecute(String response) {
				try {
					
					JSONObject obj = new JSONObject(response);
					int claimId = obj.getInt("X-CLAIM-ID");
					intent.putExtra("claimId", claimId);
					
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		}; 
		
		Toast.makeText(this, "Claim Sent", Toast.LENGTH_SHORT).show();
		startActivity(intent);
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true; 
	}


}
