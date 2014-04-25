package com.megasoft.entangle;


import com.megasoft.config.Config;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
<<<<<<< HEAD
		startActivity(new Intent(this,OfferActivity.class));
=======
		Intent intent = new Intent(this, ChangeOfferPriceActivity.class);
		intent.putExtra("requestId", 5);
		intent.putExtra("offerId", 5);
		intent.putExtra(Config.API_SESSION_ID, "5");
		startActivity(intent);
>>>>>>> 5f748ce5d247c439cb136e1b961130114cb2d4c5
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
