package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.Button;

public class Activity_Claim extends Activity {  //should be included in the request Activity
	
	/**
	 * This method uses a common intent to send an email to be shared via social networking apps ex: gmail
	 * by clicking on the claim button.
	 */

	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.claim);

		final Intent intent = new Intent(this, Activity_Claim.class);
		startActivity(intent);
		
		final int tangleId = savedInstanceState.getInt("tangleId");
		
	//	GetRequest req = new GetRequest("http://entangle2.apiary.io/tangle/" + tangleID + "/offerer_claim");
		
		final String sessionId = savedInstanceState.getString("X-SESSION-ID");
		
		intent.putExtras(savedInstanceState);
		
		Button claim = (Button) findViewById(R.id.claim);

		claim.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View view) {	
			
			GetRequest req1 = new GetRequest("http://entangle2.apiary.io/tangle/" + tangleId + "/getRecieverMail"){
				@Override
				protected void onPostExecute(String response) {
					// TODO Auto-generated method stub
					try {
						
						JSONObject obj = new JSONObject(response);
						String to = obj.getString("X-TANGLE_OWNER-MAIL");
						intent.putExtra("to_email", to);
						
					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					
				}
			};
			
			GetRequest req2 = new GetRequest("http://entangle2.apiary.io/session/" + sessionId + "/getSenderMail") {
			protected void onPostExecute(String response) {
				// TODO Auto-generated method stub
				try {
					
					JSONObject obj = new JSONObject(response);
					String from = obj.getString("X-OFFERER-MAIL");
					intent.putExtra("from_email", from);
					
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				
			}
		};
		
		 PostRequest req3 = new PostRequest("http://entangle2.apiary.io/claim" + tangleId + "/createClaim"){
			 protected void onPostExecute(String response) {
					// TODO Auto-generated method stub
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
	        req3.addHeader("X-SESSION-ID", "salma");
	        req3.execute();
		
		String to = intent.getStringExtra("to_email");
		String from = intent.getStringExtra("from_email");
		Intent emailIntent = new Intent(Intent.ACTION_SEND);
		emailIntent.setData(Uri.parse("mailto:"));
		emailIntent.putExtra(Intent.EXTRA_EMAIL, to);
		emailIntent.putExtra(Intent.EXTRA_EMAIL, from);
		emailIntent.putExtra(Intent.EXTRA_SUBJECT, "subject");
		emailIntent.putExtra(Intent.EXTRA_TEXT, "body");
		emailIntent.setType("message/rfc822");
		startActivity(Intent.createChooser(emailIntent, "Send Mail Using"));
                   
		}
	});
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
}
