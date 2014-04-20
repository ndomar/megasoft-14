package com.megasoft.entangle;

import com.megasoft.requests.GetRequest;
import android.widget.TextView;
import org.json.JSONException;
import android.content.Intent;
import android.app.Activity;
import org.json.JSONObject;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;

public class OfferActivity extends Activity {

	TextView requestDescription; 
	TextView offerDescription;
	TextView offerDeadline;
	TextView requesterName;
	TextView offererName;
	TextView offerStatus;
	TextView offerPrice;
	TextView offerDate;
	int tangleId;
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
		String link = "http://entangle2.apiary-mock.com/offer/" + offerId +"/";
		
		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon = new JSONObject(response);
						tangleId = jSon.getInt("tangleId");
						JSONObject requestInformation = jSon.getJSONObject("requestInformation");
						JSONObject offerInformation = jSon.getJSONObject("offerInformation");
						viewRequestInfo(requestInformation);
						viewOfferInfo(offerInformation);	
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			}
		};
		request.execute();
		
		}
		
	
	public void viewRequestInfo(JSONObject requestInformation) {
			try {
				requesterName.setText(requestInformation.getString("requesterName"));
				requestDescription.setText(requestInformation.getString("requestDescription"));
				
				final int userId = requestInformation.getInt("requesterID");
				final int requestId = requestInformation.getInt("requestID");
				
				requesterName.setOnClickListener(new View.OnClickListener() {
					@Override
					public void onClick(View v) {
						goToProfile(userId);
					}
				});
				
				requestDescription.setOnClickListener(new View.OnClickListener() {	
					@Override
					public void onClick(View v) {
						goToRequest(requestId);	
					}
				});
				
			} catch (JSONException e) {
				e.printStackTrace();
			}
			
		}
			
	
	public void viewOfferInfo(JSONObject offerInformation) {
		
		try {
			offerDescription.setText(offerInformation.getString("offerDescription"));
			offerDeadline.setText(offerInformation.getString("offerDeadline"));
			offererName.setText(offerInformation.getString("offererName"));
			offerDate.setText(offerInformation.getString("offerDate"));
			offerPrice.setTag(offerInformation.getInt("offerPrice"));
			
			final int userId = offerInformation.getInt("offererID");
			int status = offerInformation.getInt("offerStatus");

			if(status == 0) 
				offerStatus.setText("New");
			 else if(status == 1)
				 offerStatus.setText("In Progress");
			 else 
				 offerStatus.setText("Done");
			
			offererName.setOnClickListener(new View.OnClickListener() {
				
				@Override
				public void onClick(View v) {
					goToProfile(userId);
				}
			});
		} catch (JSONException e) {
			e.printStackTrace();
		}		
	}
	
	public void goToProfile(int userId) {
		Intent profile = new Intent(this,ProfileActivity.class);
		profile.putExtra("user id", userId);
		profile.putExtra("tangle id", this.tangleId);
		startActivity(profile);		
	}
	
	public void goToRequest(int requestId) {
		Intent request = new Intent(this,RequestActivity.class);
		request.putExtra("request id", requestId);
		startActivity(request);
	}
	

}
