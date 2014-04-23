package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.Toast;

public class Offer extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
		searchOffer(1);

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.offer, menu);
		return true;
	}

	/**
	 * this searches for details of an offer and previews it
	 * @param  Int OfferId offer ID
	 * @return None
	 * @author mohamedzayan
	 */
	public void searchOffer(int OfferId) {

		GetRequest request = new GetRequest(Config.API_BASE_URL + "/request/"
				+ 1 + "/offer/" + OfferId) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					JSONObject jresponse;
					try {
						jresponse = new JSONObject(response);
						EditText priceText = (EditText) findViewById(R.id.priceTextField);
						priceText.setText(jresponse.getString("price"));
						priceText.setEnabled(false);
						EditText dateText = (EditText) findViewById(R.id.dateTextField);
						dateText.setText(jresponse.getString("date"));
						dateText.setEnabled(false);
						EditText descriptionText = (EditText) findViewById(R.id.descriptionTextField);
						descriptionText.setText(jresponse
								.getString("description"));
						descriptionText.setEnabled(false);
						EditText deadLineText = (EditText) findViewById(R.id.expectedDeadLineTextField);
						deadLineText.setText(jresponse
								.getString("expecteddeadline"));
						deadLineText.setEnabled(false);

					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}

				}
			}
		};
		request.addHeader("X-SESSION-ID", "first");
		request.execute();

	}

	/**
	 * this checks if an offer is already marked as done or not accepted.if
	 * neither it navigates to the actual marking method
	 * @param  View view The checkbox clicked
	 * @return None
	 * @author mohamedzayan
	 */
	public void markCheck(View view) {
		GetRequest initRequest = new GetRequest(Config.API_BASE_URL
				+ "/request/" + 1 + "/offers/" + 1) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					JSONObject jresponse;
					try {
						jresponse = new JSONObject(response);
						if (jresponse.getString("status").equals("0")
								|| jresponse.getString("status").equals("2")) {
							Toast error = Toast.makeText(
									getApplicationContext(), "Error",
									Toast.LENGTH_LONG);
							error.show();
						} else {
							markAsDone(1);
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
	 * this marks an accepted offer as done
	 * @param  Int OfferId offer ID
	 * @return None
	 * @author mohamedzayan
	 */
	public void markAsDone(int Offerid) {

		JSONObject json = new JSONObject();
		try {
			json.put("status", "2");
		} catch (JSONException e) {
			e.printStackTrace();
		}
		PostRequest request = new PostRequest(Config.API_BASE_URL + "/request/"
				+ Offerid) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 201) {
					Toast success = Toast.makeText(getApplicationContext(),
							"Marked as done", Toast.LENGTH_LONG);
					success.show();
					CheckBox checkbox1 = (CheckBox) findViewById(R.id.checkBox1);
					checkbox1.setEnabled(false);
				}
			}

		};
		request.addHeader("X-SESSION-ID", "asdasdasdsadasdasd");
		request.setBody(json);
		request.execute();

	}
}