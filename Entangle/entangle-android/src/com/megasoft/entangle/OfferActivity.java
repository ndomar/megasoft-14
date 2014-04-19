package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.widget.TextView;

public class OfferActivity extends Activity {

	TextView requestDescription; 
	TextView offerDescription;
	TextView offerDeadline;
	TextView requesterName;
	TextView offererName;
	TextView offerStatus;
	TextView offerPrice;
	TextView offerDate;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
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
		
		
	}
	
	public void viewRequestInfo() {
		
	}
	
	public void viewOfferInfo() {
		
	}

}
