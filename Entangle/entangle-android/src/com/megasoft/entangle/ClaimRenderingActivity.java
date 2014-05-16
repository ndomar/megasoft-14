package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.method.HideReturnsTransformationMethod;
import android.widget.TextView;
import android.widget.Toast;
/**
 * Initializes a claim report for the offerer, requester, tangle owner
 * 
 * @author Salma Amr
 *
 */
public class ClaimRenderingActivity extends Activity {

	/**
	 * String holds the creation date of the claim
	 */
	String claimDate = "";
	/**
	 * String holds the name of the claimer
	 */
	String claimerName = "";
	/**
	 * String holds the offerer name
	 */
	String offererName = "";
	/**
	 * String holds the offerer email
	 */
	String offererEmail = "";
	/**
	 * String holds the requester name
	 */
	String requesterName = "";
	/**
	 * String holds the requester email
	 */
	String requesterEmail = "";
	/**
	 * String holds the name of the tangle owner
	 */
	String tangleOwnerName = "";
	/**
	 * String holds the email of the tangle owner
	 */
	String tangleOwnerEmail = "";
	/**
	 * String holds the name of the tangle
	 */
	String tangleName = "";
	/**
	 * String holds the message of the claim
	 */
	String claimMessage = "";
	/**
	 * String holds the shared preferences
	 */
	private SharedPreferences settings;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.setData();
		setContentView(R.layout.activity_claim_render);
	}

	/**
	 * This method sets the data of the claim report into its right positions
	 * after executing the request which gets the data
	 * 
	 * @return None
	 * @author Salma Amr
	 */
	public void setData() {

		this.settings = getSharedPreferences(Config.SETTING, 0);
		String sessionID = settings.getString(Config.SESSION_ID, "");

		int claimId = (int) getIntent().getIntExtra("claimId", -1);
		int offerId = (int) getIntent().getIntExtra("offerId", -1);

		GetRequest requestClaimReport = new GetRequest(
				Config.API_BASE_URL_SERVER + "/claimReport/" + claimId
						+ "/claim/" + offerId + "/offer") {

			@Override
			protected void onPostExecute(String response) {
				try {
					if (this.getStatusCode() == 200) {
						JSONObject object = new JSONObject(response);

						claimDate = object.getString("claimDate");
						claimerName = object.getString("claimer");
						offererName = object.getString("offerer");
						offererEmail = object.getString("offererEmail");
						requesterName = object.getString("requester");
						requesterEmail = object.getString("requesterEmail");
						tangleOwnerName = object.getString("tangleOwner");
						tangleOwnerEmail = object
								.getString("tangleOwnerEmail");
						tangleName = object.getString("tangle");
						claimMessage = object.getString("claimMessage");
						Toast.makeText(getBaseContext(),
								"Loading Claim Report", Toast.LENGTH_SHORT)
								.show();
					} else {
						Toast.makeText(getBaseContext(),
								"Something went wrong", Toast.LENGTH_SHORT)
								.show();
					}

				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};
		requestClaimReport.addHeader("X-SESSION-ID", sessionID);
		requestClaimReport.execute();
		TextView date = (TextView) findViewById(R.id.date);
		date.setText(claimDate);
		TextView claimer = (TextView) findViewById(R.id.claimerName);
		claimer.setText(claimerName);
		TextView offerer = (TextView) findViewById(R.id.offererName);
		offerer.setText(offererName);
		TextView offererEmail = (TextView) findViewById(R.id.offererEmailText);
		offererEmail.setText(this.offererEmail);
		TextView requester = (TextView) findViewById(R.id.requesterName);
		requester.setText(requesterName);
		TextView requesterEmail = (TextView) findViewById(R.id.requesterEmailText);
		requesterEmail.setText(this.requesterEmail);
		TextView tangleOwnerName = (TextView) findViewById(R.id.tangleOwnerName);
		tangleOwnerName.setText(this.tangleOwnerName);
		TextView tangleOwnerEmail = (TextView) findViewById(R.id.tangleOwnerEmailText);
		tangleOwnerEmail.setText(this.tangleOwnerEmail);
		TextView tangle = (TextView) findViewById(R.id.tangleNameText);
		tangle.setText(this.tangleName);
		TextView claimMssg = (TextView) findViewById(R.id.mssgText);
		claimMssg.setText(this.claimMessage);

	}

}
