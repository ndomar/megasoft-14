package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.text.method.ScrollingMovementMethod;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.Scroller;
import android.widget.Toast;

public class Claim extends Activity {
	/**
	 * String holding the mail of the claim sender
	 */
	String offererMail;
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
		setContentView(R.layout.claimform);
		offererMail = this.getIntent().getStringExtra("sender");
		tangleOwenerMail = this.getIntent().getStringExtra("receiver");
		EditText fromMail = (EditText) findViewById(R.id.fromText);
		EditText toMail = (EditText) findViewById(R.id.toText);
		EditText subject = (EditText) findViewById(R.id.subjectText);
		EditText mssg = (EditText) findViewById(R.id.mssgText);
		subject.setScroller(new Scroller(getBaseContext()));
		subject.setMaxLines(2);
		subject.setVerticalScrollBarEnabled(true);
		subject.setMovementMethod(new ScrollingMovementMethod());
		subject.setMinLines(2);
		mssg.setScroller(new Scroller(getBaseContext()));
		mssg.setMaxLines(9);
		mssg.setVerticalScrollBarEnabled(true);
		mssg.setMovementMethod(new ScrollingMovementMethod());
		mssg.setMinLines(9);
		fromMail.setText(offererMail);
		toMail.setText(tangleOwenerMail);
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
		subject = ((EditText) findViewById(R.id.subjectText)).getText()
				.toString();
		mssgBody = ((EditText) findViewById(R.id.mssgText)).getText()
				.toString();
		if (offererMail.equals("") || tangleOwenerMail.equals("")) {
			Toast.makeText(this, "Please enter valid emails",
					Toast.LENGTH_SHORT).show();
		} else if (mssgBody.equals("")) {
			Toast.makeText(this, "Msssg body missing", Toast.LENGTH_LONG)
					.show();
		} else {
			if (subject.equals("")) {
				Toast.makeText(this, "Subject is missing", Toast.LENGTH_LONG)
						.show();
			}
			JSONObject object = new JSONObject();
			try {
				object.put("X-SENDER-MAIL", offererMail);
				object.put("X-RECEIVER-MAIL", tangleOwenerMail);
				object.put("X-SUBJECT", subject);
				object.put("X-MSSGBODY", mssgBody);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			int tangleID = (int) getIntent().getIntExtra("tangleID", 0);
			PostRequest postSubject = new PostRequest(Config.API_BASE_URL
					+ "/claim/" + tangleID + "/sendClaim") {

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
			String sessionID = (String) getIntent().getCharSequenceExtra(
					"sessionID");
			postSubject.setBody(object);
			postSubject.addHeader("X-SESSION-ID", sessionID);
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
