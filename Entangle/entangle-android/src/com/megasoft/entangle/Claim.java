package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.text.method.LinkMovementMethod;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;
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
		setContentView(R.layout.activity_claimform);
		TextView link = (TextView) findViewById(R.id.link);
		link.setMovementMethod(LinkMovementMethod.getInstance());
		getActionBar().hide();
		claimerMail = this.getIntent().getStringExtra("sender");
		tangleOwenerMail = this.getIntent().getStringExtra("receiver");
	}
	
	public void cancel() { 
		this.finish();
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

			int requestId = (int) getIntent().getIntExtra("requestId", -1);
			PostRequest postSubject = new PostRequest(Config.API_BASE_URL_SERVER
					+ "/claim/" + requestId + "/sendClaim") {

				protected void onPostExecute(String response) {
					try {
						if (this.getStatusCode() == 200) {
							JSONObject obj = new JSONObject(response);
							int claimId = obj.getInt("X-CLAIM-ID");
							intent.putExtra("claimId", claimId);
							Toast.makeText(getBaseContext(), "Claim Sent", Toast.LENGTH_SHORT).show();
							intent.addFlags(intent.FLAG_ACTIVITY_CLEAR_TOP);
							startActivity(intent);
						} else {
							Toast.makeText(getBaseContext(), "Something went wrong",
									Toast.LENGTH_SHORT).show();
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
		}

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
