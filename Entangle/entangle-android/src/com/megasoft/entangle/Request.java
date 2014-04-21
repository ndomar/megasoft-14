package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.requests.GetRequest;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.Toast;

public class Request extends Activity {
	/**
	 * String holding the sender of the claim
	 */
	String offererMail = "";
	/**
	 * string holding the receiver of the claim
	 */
	String tangleOwnerMail = "";

	/**
	 * This method loads the request form
	 * 
	 * @param Bundle
	 *            savedInstanceState holds the request bundle having claim
	 *            button
	 * @return None
	 * @author Salma Amr
	 */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.request);
	}

	/**
	 * This method gets the email of both the offerer and the tangle owner after
	 * fetching them from the back end through the delivered json response and
	 * sends these mails to the claim form session
	 * @param View view hold the claim button
	 * @return None
	 * @author Salma Amr
	 */
	public void startClaimForm(View view) {
		final Intent intent = new Intent(this, Claim.class);
		String sessionID = (String) getIntent().getCharSequenceExtra(
				"sessionID");
		int tangleID = (int) getIntent().getIntExtra("tangleID", 0);
		GetRequest requestTangleOwnerMail = new GetRequest(
				"http://entangle2.apiary.io/tangleOwnerAndOffererMail/"
						+ tangleID + "/claim") {

			protected void onPostExecute(String response) {
				try {
					JSONObject object = new JSONObject(response);
					tangleOwnerMail += object.getString("X-TANGLEOWNER-MAIL");
					offererMail += object.getString("X-OFFERER-MAIL");
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};

		requestTangleOwnerMail.addHeader("X-SESSION-ID", sessionID);
		requestTangleOwnerMail.execute();
		intent.putExtra("receiver", tangleOwnerMail);
		intent.putExtra("sender", offererMail);
		intent.putExtra("tangleID", tangleID);
		intent.putExtra("sessionID", sessionID);
		Toast.makeText(this, "Loading Claim Form", Toast.LENGTH_SHORT).show();
		startActivity(intent);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
