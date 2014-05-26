package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;
import com.megasoft.utils.UI;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.method.LinkMovementMethod;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

/**
 * Creates the claim
 * 
 * @author Salma Amr
 * 
 */
public class Claim extends Activity {

	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;
	/**
	 * String holding the message of the sent claim it self
	 */
	String mssgBody;

	/**
	 * This sets the email of the tangle owner and the requester into a non
	 * editable edit text, also it sets the view of the claim form.
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
		getActionBar().hide();
	}

	/**
	 * This method finishes the activity on clicking the cancel button.
	 * 
	 * @param view
	 * @author Salma Amr
	 */
	public void cancel(View view) {
		this.finish();
	}

	/**
	 * This method creates the claim form after making sure of entering the body
	 * and the subject of the claim, it creates a claim id.
	 * 
	 * @param View
	 *            view the claim button clicked
	 * @return None
	 * @author Salma Amr
	 */
	public void sendClaimForm(View view) {

		final Intent intent = new Intent(this, OfferActivity.class);
		mssgBody = ((EditText) findViewById(R.id.mssgText)).getText()
				.toString();
		if (mssgBody.equals("")) {
			Toast.makeText(this, "Msssg body missing", Toast.LENGTH_LONG)
					.show();
		} else {

			JSONObject object = new JSONObject();
			try {
				object.put("claimMessage", mssgBody);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

			int requestId = (int) getIntent().getIntExtra("requestId", -1);
			int offerId = (int) getIntent().getIntExtra("offerId", -1);
			PostRequest postSubject = new PostRequest(
					Config.API_BASE_URL + "/claim/" + requestId
							+ "/sendClaim/" + offerId + "/user") {

				protected void onPostExecute(String response) {
					try {
						if (this.getStatusCode() == 201) {
							JSONObject obj = new JSONObject(response);
							int claimId = obj.getInt("claimId");
							intent.putExtra("claimId", claimId);
							Toast.makeText(getBaseContext(), "Claim Sent!",
									Toast.LENGTH_SHORT).show();
							startActivity(intent);
						} else {
							UI.makeToast(getBaseContext(), "Something went wrong", Toast.LENGTH_SHORT);
						}

					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				}
			};
			this.settings = getSharedPreferences(Config.SETTING, 0);
			String sessionID = settings.getString(Config.SESSION_ID, "");
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
