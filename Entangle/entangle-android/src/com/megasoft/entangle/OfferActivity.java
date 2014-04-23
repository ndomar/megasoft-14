package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;

import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.LinearLayout;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

public class OfferActivity extends Activity {
	/**
	 * the session Id
	 */
	String sessionId;
	/**
	 * settings
	 */
	SharedPreferences settings;
	/**
	 * this activity
	 */

	final Activity self = this;
	/**
	 * offer Id
	 */
	int offerId;
	/**
	 * JSON object to be received from Get request
	 */
	JSONObject returnedResponse = null;
	/**
	 * layout
	 */
	LinearLayout layout;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
		Intent intent = getIntent();
		offerId = intent.getIntExtra("offerId", -1);
		validate();
	}

	/**
	 * this checks if offer can be accepted and calls addAcceptButton() if it
	 * can
	 * 
	 * @param none
	 * @return None
	 * @author sak93
	 */
	public void validate() {
		GetRequest request = new GetRequest(Config.API_BASE_URL + "/offer/"
				+ offerId) {

			protected void onPostExecute(String response) {
				try {

					JSONObject jsonResponse = new JSONObject(response);
					JSONObject offerDetails = (JSONObject) jsonResponse
							.get("offerInformation");
					JSONObject requestDetails = (JSONObject) jsonResponse
							.get("requestInformation");
					int requestStatus = (Integer) requestDetails
							.get("requestStatus");
					if (requestStatus != 0) {
						return;
					} else {
						int offerStatus = (Integer) offerDetails
								.get("offerStatus");
						if (offerStatus == 0) {
							addAcceptButton();
						}
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}

		};
		request.addHeader(Config.API_SESSION_ID, sessionId);
		request.execute();
	}

	/**
	 * this adds a button which if clicked sends a POST method to update the
	 * offer as accepted
	 * 
	 * @param none
	 * @return None
	 * @author sak93
	 */
	public void addAcceptButton() throws JSONException {
		final Button button = (Button) findViewById(R.id.button1);
		button.setText("Accept");
		button.setVisibility(1);
		returnedResponse = new JSONObject();
		returnedResponse.put("offerId", "" + offerId);
		button.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				PostRequest r = new PostRequest(Config.API_BASE_URL
						+ "/accept/offer");
				r.setBody(returnedResponse);
				r.addHeader("x-session-id", Config.SESSION_ID);
				r.execute();
				button.setVisibility(View.GONE);

			}
		});

	}

}
