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

/**
 * View an offer given the offer Id 
 * @author Almgohar
 */
public class OfferActivity extends Activity {

	/**
	 * The TextView that holds the request's description
	 */
	TextView requestDescription; 
	
	/**
	 * The TextView that holds the offer's description
	 */
	TextView offerDescription;
	
	/**
	 * The TextView that holds the offer's expected deadline
	 */
	TextView offerDeadline;
	
	/**
	 * The TextView that holds the requester's name
	 */
	TextView requesterName;
	
	/**
	 * The TextView that holds the offerer's name
	 */
	TextView offererName;
	
	/**
	 * The TextView that holds the offer's status
	 */
	TextView offerStatus;
	
	/**
	 * The TextView that holds the offer's price
	 */
	TextView offerPrice;
	
	/**
	 * The TextView that holds the date on which the offer was created
	 */
	TextView offerDate;
	
	/**
	 * The tangle Id
	 */
	int tangleId;
	
	/**
	 * The offer Id
	 */
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
	
	/**
	 * Initializes all views to link to the XML views
	 * Sends a GET request and get the JSon response
	 * Calls the ViewRequestInformation method
	 * Calls the ViewOfferInformation method
	 * @author Almgohar
	 */
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
		
	/**
	 *  Retrieves the required request information from the JSonObject
	 *  Views the request information
	 * @param JSonObject requestInformation
	 * @author Almgohar
	 */
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
			
	/**
	 * Retrieves the required offer information from the JSonObject
	 * Views the offer information
	 * @param JSonObject offerInformation
	 * @author Almgohar
	 */
	public void viewOfferInfo(JSONObject offerInformation) {
		
		try {
			offerDescription.setText(offerInformation.getString("offerDescription"));
			offerDeadline.setText(offerInformation.getString("offerDeadline"));
			offererName.setText(offerInformation.getString("offererName"));
			offerDate.setText(offerInformation.getString("offerDate"));
			offerPrice.setText(Integer.toString(offerInformation.getInt("offerPrice")));
			
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
	
	/**
	 * Redirects to a user's profile given his id
	 * @param int userId
	 * @author Almgohar
	 */
	public void goToProfile(int userId) {
		Intent profile = new Intent(this,ProfileActivity.class);
		profile.putExtra("user id", userId);
		profile.putExtra("tangle id", this.tangleId);
		startActivity(profile);		
	}
	
	/**
	 * Redirects to a request given its id
	 * @param int requestId
	 * @author Almgohar
	 */
	public void goToRequest(int requestId) {
		Intent request = new Intent(this,RequestActivity.class);
		request.putExtra("request id", requestId);
		startActivity(request);
	}
}
