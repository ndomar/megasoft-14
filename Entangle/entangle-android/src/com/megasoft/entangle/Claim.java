package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

public class Claim extends Activity {
	/**
	 * String holding the mail of the claim sender
	 */
	String claimerMail;
	/**
	 * String holding the mail of the claim receiver
	 */
	String tangleOwenerMail;
	/**
	 * String holding the subject of the claim
	 */
	String subject;
	/**
	 * String holding the message of the sent claim it self
	 */
	String mssgBody;
	/**
	 * this boolean indicates whether the request was successful or not
	 */
	boolean connection = true;

	/**
	 * this sets the email of the tangle owner and the requester into a non
	 * editable edit text, also it sets the view of the claim form
	 * 
	 * @param Bundle
	 *            savedInstanceState android bundle
	 * @return None
	 * @author Salma Amr
	 */

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.claimform);
		claimerMail = this.getIntent().getStringExtra("sender");
		tangleOwenerMail = this.getIntent().getStringExtra("receiver");
	}

	/**
	 * This method creates the claim form after making sure of entering the body
	 * and the subject of the claim, it creates a claim id
	 * 
	 * @param View
	 *            view the claim button clicked
	 * @return None
	 * @author Salma Amr
	 */
	public void sendClaimForm(View view) {

		final Intent intent = new Intent(this, Request.class);
		mssgBody = ((EditText) findViewById(R.id.mssgText)).getText()
				.toString();
		if (mssgBody.equals("")) {
			Toast.makeText(this, "Msssg body missing", Toast.LENGTH_LONG)
					.show();
		} else {

			JSONObject object = new JSONObject();
			try {
				object.put("X-SENDER-MAIL", claimerMail);
				object.put("X-RECEIVER-MAIL", tangleOwenerMail);
				object.put("X-MSSGBODY", mssgBody);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

			int requestId = (int) getIntent().getIntExtra("requestId", 0);
			PostRequest postSubject = new PostRequest(Config.API_BASE_URL
					+ "/claim/" + requestId + "/sendClaim") {

				protected void onPostExecute(String response) {
					try {
						if (this.getStatusCode() == 200) {
							JSONObject obj = new JSONObject(response);
							int claimId = obj.getInt("X-CLAIM-ID");
							intent.putExtra("claimId", claimId);
						} else {
							connection = false;
						}

					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				}
			};

			String sessionID = (String) getIntent().getCharSequenceExtra(
					"sessionID");
			postSubject.setBody(object);
			postSubject.addHeader("X-SESSION-ID", sessionID);
			postSubject.execute();
			if (!connection) {
				Toast.makeText(this, "Something went wrong",
						Toast.LENGTH_SHORT).show();
			} else {
				Toast.makeText(this, "Claim Sent", Toast.LENGTH_SHORT).show();
				startActivity(intent);
			}

		}

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
