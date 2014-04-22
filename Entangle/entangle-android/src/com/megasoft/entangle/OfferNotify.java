package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.view.View;
import android.widget.Toast;

public class OfferNotify extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer_notify);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.offer_notify, menu);
		return true;
	}
	
	/**
	 * this checks if an offer is already marked as done or not accepted.if
	 * neither it navigates to the actual notifying method
	 * @param View view The Button clicked
	 * @return None
	 * @author mohamedzayan
	 */
	public void notifyCheck(View view) {
		GetRequest initRequest = new GetRequest(
				"http://test1450.apiary-mock.com/request/" + 1 + "/offers/" + 1) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					JSONObject x;
					try {
						x = new JSONObject(response);
						if (x.getString("status").equals("0")
								|| x.getString("status").equals("2")) {
							Toast error = Toast.makeText(
									getApplicationContext(), "Error",
									Toast.LENGTH_LONG);
							error.show();
						} else {
							sendNotification(1);
						}

					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
				}
			}
		};
		initRequest.addHeader("X-SESSION-ID", "second");
		initRequest.execute();
	}
	/**
	 * this sends the actual notification
	 * @param  Int OfferId offer ID
	 * @return None
	 * @author mohamedzayan
	 */
	public void sendNotification(int Offerid) {
		PostRequest request = new PostRequest(
				"http://entangle2.apiary-mock.com/request/" + Offerid) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 201) {
					Toast success = Toast.makeText(getApplicationContext(),
							"Requester has been notified", Toast.LENGTH_LONG);
					success.show();
				}
			}

		};
		request.addHeader("X-SESSION-ID", "asdasdasdsadasdasd");
		request.execute();
	}

}
