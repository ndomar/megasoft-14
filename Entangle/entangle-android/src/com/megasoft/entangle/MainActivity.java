package com.megasoft.entangle;

import org.apache.http.client.methods.HttpPost;

import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.os.Bundle;
import android.content.Intent;
import android.view.Menu;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		goToOffer();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	/**
	 * this navigates to offer activity
	 * @return None
	 * @author mohamedzayan
	 */
	public void goToOffer() {
		Intent intent = new Intent(this, Offer.class);
		startActivity(intent);
	}

}
