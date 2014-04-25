package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;

public class MainActivity extends Activity {
	private Button login;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_main);  
		//startActivity((new Intent(this,TangleStreamActivity.class)));
		startActivity((new Intent(this,OfferActivity.class)));


	
		setContentView(R.layout.activity_main);

		Intent intent = new Intent(this, ChangeOfferPriceActivity.class);
		intent.putExtra("requestId", 5);
		intent.putExtra("offerId", 5);
		intent.putExtra(Config.API_SESSION_ID, "5");
		startActivity(intent);

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		getMenuInflater().inflate(R.menu.main, menu);
		return true;

	}

<<<<<<< HEAD

=======
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {

		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
>>>>>>> 25f21cc9872fca3e52df1cb40b444abca9f437c5

}
