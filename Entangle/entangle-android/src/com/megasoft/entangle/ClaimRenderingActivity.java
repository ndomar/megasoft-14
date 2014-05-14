package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.widget.TextView;
import android.widget.Toast;

public class ClaimRenderingActivity extends Activity {

	String claimDate = "";
	String claimerName = "";
	String offererName = "";
	String offererEmail = "";
	String requesterName = "";
	String requesterEmail = "";
	String tangleOwnerName = "";
	String tangleOwnerEmail = "";
	String tangleName = "";
	String claimMessage = "";
	private SharedPreferences settings;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_claim_render);
	}

	public void setData() {

		final Intent intent = new Intent(this, OfferActivity.class);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		String sessionID = settings.getString(Config.SESSION_ID, "");

		int claimId = (int) getIntent().getIntExtra("claimId", -1);
		int offerId = (int) getIntent().getIntExtra("offerId", -1);

		GetRequest requestClaimReport = new GetRequest(
				Config.API_BASE_URL_SERVER + "/claimReport/" + claimId
						+ "/claim/" + offerId + "/offer") {

			protected void onPostExecute(String response) {
				try {
					if (this.getStatusCode() == 200) {
						JSONObject object = new JSONObject(response);

						claimDate += object.getString("X-CLAIM-DATE");
						claimerName += object.getString("X-CLAIMER");
						offererName += object.getString("X-OFFERER");
						offererEmail += object.getString("X-OFFERER-EMAIL");
						requesterName += object.getString("X-REQUESTER");
						requesterEmail += object.getString("X-REQUESTER-EMAIL");
						tangleOwnerName += object.getString("X-TANGLE-OWNER");
						tangleOwnerEmail += object
								.getString("X-TANGLE-OWNER-EMAIL");
						tangleName += object.getString("X-TANGLE");
						claimMessage += object.getString("X-CLAIM-MESSAGE");
						Toast.makeText(getBaseContext(),
								"Loading Claim Report", Toast.LENGTH_SHORT)
								.show();
						startActivity(intent);
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
		date.setText(this.claimDate);

		TextView claimer = (TextView) findViewById(R.id.claimerName);
		claimer.setText(this.claimerName);

		TextView offerer = (TextView) findViewById(R.id.offererName);
		offerer.setText(this.offererName);

		TextView offererEmail = (TextView) findViewById(R.id.offererEmailText);
		offererEmail.setText(this.offererEmail);

		TextView requester = (TextView) findViewById(R.id.requesterName);
		requester.setText(this.requesterName);

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
