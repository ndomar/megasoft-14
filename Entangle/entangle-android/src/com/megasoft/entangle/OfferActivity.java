package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;

public class OfferActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		//getMenuInflater().inflate(R.menu.offer, menu);
		return true;
	}

}
