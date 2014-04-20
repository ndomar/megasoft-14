package com.megasoft.entangle;

import com.megasoft.requests.GetRequest;
import android.widget.TextView;
import org.json.JSONException;
import android.content.Intent;
import android.app.Activity;
import org.json.JSONObject;
import android.os.Bundle;
import android.view.Menu;

public class OfferActivity extends Activity {

	TextView requestDescription; 
	TextView offerDescription;
	TextView offerDeadline;
	TextView requesterName;
	TextView offererName;
	TextView offerStatus;
	TextView offerPrice;
	TextView offerDate;
	int offerId;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
		Intent intent = new Intent();
		offerId = intent.getIntExtra("offerID", 3);
		viewOffer();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.offer, menu);
		return true;
	}
	
	public void viewOffer() {
		requestDescription = (TextView) findViewById(R.id.request_description);
		offerDescription = (TextView) findViewById(R.id.offer_description);
		offerDeadline = (TextView) findViewById(R.id.offer_deadline);
		requesterName = (TextView) findViewById(R.id.requester_name);
		offererName = (TextView) findViewById(R.id.offerer_name);
		offerStatus = (TextView) findViewById(R.id.offer_status);
		offerPrice = (TextView) findViewById(R.id.offer_price);
		offerDate = (TextView) findViewById(R.id.offer_date);
		viewRequestInfo();
	}
	
	public void viewRequestInfo() {
		
			String link = "http://entangle2.apiary-mock.com/offer/" + offerId +"/";
			
			GetRequest request = new GetRequest(link) {
				protected void onPostExecute(String response) {
					if (this.getStatusCode() == 200) {
						try {
							JSONObject jSon = new JSONObject(response);
							JSONObject requestInformation = jSon.getJSONObject("requestInformation");
							JSONObject offerInformation = jSon.getJSONObject("offerInformation");
							requesterName.setText(requestInformation.getString("requesterName"));
							requestDescription.setText(requestInformation.getString("requestDescription"));
							
							viewOfferInfo(offerInformation);	
						} catch (JSONException e) {
							e.printStackTrace();
						}
					}
				}
			};
			request.execute();
		}
			
	
	public void viewOfferInfo(JSONObject offerInformation) {
		
	}

}
