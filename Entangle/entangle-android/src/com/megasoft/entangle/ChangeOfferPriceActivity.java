package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.PostRequest;
import com.megasoft.config.Config;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;

public class ChangeOfferPriceActivity extends Activity {

	int requestId;
	int offerId;
	String sessionId;
	SharedPreferences settings;
	public static final int BUTTON_POSITIVE = 0xffffffff;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_change_offer_price);

		this.requestId = getIntent().getIntExtra("requestId", -1);
		this.offerId = getIntent().getIntExtra("offerId", -1);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.change_offer_price, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}

	public void changePrice(View view) {
		EditText newPrice = (EditText) findViewById(R.id.newOfferPrice);
		if ((newPrice.getText().toString()).equals("")) {
			showMessage("PLEASE ENTER A NEW PRICE");
		} else {
			if (sessionId == "") {
				showMessage("SESSION EXPIRED, PLEASE RELOGIN");
				goToHomeHelper();
			} else {
				if (requestId == -1 || offerId == -1) {
					showMessage("INVALID OFFER, TRY AGAIN LATER");
				} else {
					sendPriceToServer();
				}
			}
		}
	}

	public void sendPriceToServer() {
		PostRequest imagePostRequest = new PostRequest(
				Config.API_BASE_URL + "/request/" + requestId
						+ "/offers/" + offerId + "/changeprice") {
			protected void onPostExecute(String response) {
				if (!(this.getStatusCode() == 200)) {
					showMessage("ERROR, TRY AGAIN LATER");
				} else {
					goToHomePage();
				}
			}
		};
		JSONObject priceJSON = new JSONObject();
		try {
			priceJSON.put("newPrice",
					((EditText) findViewById(R.id.newOfferPrice)).getText()
							.toString());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		imagePostRequest.setBody(priceJSON);
		imagePostRequest.addHeader(Config.API_SESSION_ID, "asdfc");
		imagePostRequest.execute();
	}

	public void showMessage(String message) {
		AlertDialog ad = new AlertDialog.Builder(this).create();
		ad.setCancelable(false);
		ad.setMessage(message);
		ad.setButton(BUTTON_POSITIVE, "OK",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
					}
				});
		ad.show();
	}

	public void goToHomePage() {
		AlertDialog ad = new AlertDialog.Builder(this).create();
		ad.setCancelable(false);
		ad.setMessage("PRICE IS CHANGED SUCCESSFULLY");
		ad.setButton(BUTTON_POSITIVE, "OK",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
						goToHomeHelper();
					}
				});
		ad.show();
	}

	/**
	 * Redirects the user to the home page
	 * 
	 * @author Mansour
	 */
	public void goToHomeHelper() {
		startActivity(new Intent(this, MainActivity.class));
	}
}
