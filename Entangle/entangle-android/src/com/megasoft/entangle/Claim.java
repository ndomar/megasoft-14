package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.requests.PostRequest;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

public class Claim extends Activity {
	
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
		fromMail.setText(offererMail);
		toMail.setText(tangleOwenerMail);
	} 
	
	public void sendClaimForm(View v) { 
		
		final Intent intent = new Intent(this, Request.class);
		subject = ((EditText) findViewById(R.id.subjectText)).getText().toString();
		mssgBody = ((EditText) findViewById(R.id.mssgText)).getText().toString();
		if (offererMail.equals("") || tangleOwenerMail.equals("")) {
			Toast.makeText(this, "Please enter valid emails", Toast.LENGTH_SHORT).show();
		}
		else if (mssgBody.equals("")) {
			Toast.makeText(this, "Msssg body missing", Toast.LENGTH_LONG).show();
		}
		else {
			if (subject.equals("")) {
				Toast.makeText(this, "Subject is missing", Toast.LENGTH_LONG).show();
			}
			JSONObject object = new JSONObject();
		try {
			object.put("X-SENDER-MAIL", offererMail);
			object.put("X-RECIEVER-MAIL", tangleOwenerMail);
			object.put("X-SUBJECT", subject);
			object.put("X-MSSGBODY", mssgBody);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		int tangleID = (int) getIntent().getIntExtra("tangleID", 0);
		PostRequest postSubject = new PostRequest("http://sprint1.apiary.io/claim/" + tangleID + "/sendClaim") {
			
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
		String sessionID = (String) getIntent().getCharSequenceExtra("sessionID");
		postSubject.setBody(object);
		postSubject.addHeader("X-SESSION-ID",sessionID);
		postSubject.execute();
		Toast.makeText(this, "Claim Sent", Toast.LENGTH_SHORT).show();
		startActivity(intent);
		}
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true; 
	}


}
