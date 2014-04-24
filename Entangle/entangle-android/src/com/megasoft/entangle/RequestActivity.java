package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.view.Menu;
import android.view.View;

public class RequestActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_request);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.request_information, menu);
		return true;
	}
	/**
	 * this navigates to offer activity
	 * @param  View view The button clicked
	 * @return None
	 * @author mohamedzayan
	 */
	/*public void goToOffer(View view) {
		Intent intent = new Intent(this, Offer.class);
		startActivity(intent);

	}*/

}
