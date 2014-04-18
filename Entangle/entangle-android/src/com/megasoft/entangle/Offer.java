package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.util.Log;
import android.view.Menu;
import android.view.View;
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

	public void searchOffer(int OfferId) {

		// Creating a new Post Request

		GetRequest request = new GetRequest(
				"http://test1450.apiary-mock.com/request/" + 1 + "/offer/"
						+ OfferId) {
			protected void onPostExecute(String response) { // On Post execute
															// means after the
															// execution of the
															// request ( the
															// callback )
				if (this.getStatusCode() == 200) {
					Log.e("test", response);
					JSONObject x;
					System.out.println(response);
					try {
						x = new JSONObject(response);
						EditText textview1 = (EditText) findViewById(R.id.priceTextField);
						textview1.setText(x.getString("price"));
						EditText textview2 = (EditText) findViewById(R.id.dateTextField);
						textview2.setText(x.getString("date"));
						EditText textview3 = (EditText) findViewById(R.id.descriptionTextField);
						textview3.setText(x.getString("description"));
						EditText textview4 = (EditText) findViewById(R.id.expectedDeadLineTextField);
						textview4.setText(x.getString("expecteddeadline"));

					} catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}

					viewSuccessMessage(response);
				} else if (this.getStatusCode() == 400) {
					showErrorMessage(response);
				}
			}

			private void showErrorMessage(String response) {
				// TODO Auto-generated method stub

			}

			private void viewSuccessMessage(String response) {
				// TODO Auto-generated method stub

			}
		};
		request.addHeader("X-SESSION-ID", "first");
		// Adding the json to the body of the request
		request.execute(); // Executing the request

	}

	public void mark1(View view) {
		markAsDone(1);
	}

	public void markAsDone(int Offerid) {
		JSONObject json = new JSONObject();
		try {
			json.put("status", "3");
		} catch (JSONException e) {
			e.printStackTrace();
		}
		PostRequest request = new PostRequest(
				"http://test1450.apiary-mock.com/request/" + Offerid) {
			protected void onPostExecute(String response) { // On Post execute
															// means after the
															// execution of the
															// request ( the
															// callback )
				Log.e("test", response);
				if (this.getStatusCode() == 201) {
					Toast toast = Toast.makeText(getApplicationContext(),
							"Marked as done", Toast.LENGTH_LONG);
					toast.show();
				} else {
					Toast toast1 = Toast.makeText(getApplicationContext(),
							"Error", Toast.LENGTH_LONG);
					toast1.show();
				}
			}

		};
		request.addHeader("X-SESSION-ID", "asdasdasdsadasdasd");
		request.setBody(json); // Adding the json to the body of the request
		request.execute(); // Executing the request

	}
}
