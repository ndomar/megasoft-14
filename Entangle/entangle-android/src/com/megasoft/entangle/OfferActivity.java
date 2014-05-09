package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.app.FragmentTransaction;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.text.InputType;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.ImageRequest;
import com.megasoft.requests.PostRequest;

/**
 * View an offer given the offer Id
 * 
 * @author Almgohar
 */
public class OfferActivity extends FragmentActivity {

	/**
	 * The TextView that holds the offer's description
	 */
	private TextView offerDescription;

	/**
	 * The TextView that holds the offer's expected deadline
	 */
	private TextView offerDeadline;

	/**
	 * The TextView that holds the offerer's name
	 */
	private TextView offererName;

	/**
	 * The TextView that holds the offer's status
	 */
	private TextView offerStatus;

	/**
	 * The TextView that holds the offer's price
	 */
	private TextView offerPrice;

	/**
	 * The TextView that holds the date on which the offer was created
	 */
	private TextView offerDate;

	/**
	 * The EditText of the comment
	 */
	private EditText comment;

	/**
	 * The button that enables the user to add his comment
	 */
	private ImageView addComment;
	/**
	 * The imageView containing the offerer's photo
	 */
	private com.megasoft.entangle.views.RoundedImageView offererAvatar;

	/**
	 * The button that allows the user to accept an offer
	 */
	private Button acceptOffer;

	/**
	 * The tangle Id
	 */
	private int tangleId;

	/**
	 * The offer Id
	 */
	private int offerId;

	/**
	 * The session Id
	 */
	private String sessionId;

	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;

	/**
	 * JSON object to be received from Get request
	 */
	JSONObject returnedResponse = null;
	/**
	 * The layout containing the DeleteOffer button
	 */
	LinearLayout deleteOfferLayout;
	/**
	 * String for get offer details endpoint
	 */
	final String OFFER = "/offer/" + offerId;
	/**
	 * String for post offer endpoint
	 */
	final String ACCEPT = "/accept/offer";

	/**
	 * The id of the logged in user
	 */
	private int loggedInId;

	/**
	 * The FragmentTransaction that handles adding the fragments to the activity
	 */
	private FragmentTransaction transaction;

	private ScrollView scrollView;

	private String newPriceText;

	public static final int BUTTON_POSITIVE = 0xffffffff;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_offer);
		Intent intent = getIntent();
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.loggedInId = settings.getInt(Config.USER_ID, 1);
		this.offerId = intent.getIntExtra("offerID", 1);
		viewOffer();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.offer, menu);
		return true;
	}

	/**
	 * Initializes all views to link to the XML views Sends a GET request and
	 * get the JSon response Calls the ViewRequestInformation method Calls the
	 * ViewOfferInformation method
	 * 
	 * @author Almgohar
	 */
	public void viewOffer() {
		offererAvatar = (com.megasoft.entangle.views.RoundedImageView) findViewById(R.id.offerer_avatar);
		offerDescription = (TextView) findViewById(R.id.offer_description);
		offerDeadline = (TextView) findViewById(R.id.offer_deadline);
		offererName = (TextView) findViewById(R.id.offerer_name);
		offerStatus = (TextView) findViewById(R.id.offer_status);
		offerPrice = (TextView) findViewById(R.id.offer_price);

		offerDate = (TextView) findViewById(R.id.offer_date);
		comment = (EditText) findViewById(R.id.add_comment_field);
		addComment = (ImageView) findViewById(R.id.add_comment_button);
		scrollView = (ScrollView) findViewById(R.id.comment_area_scroll_view);
		// deleteOfferLayout = (LinearLayout)
		// findViewById(R.id.delete_offer_layout);
		acceptOffer = (Button) findViewById(R.id.accept_offer);
		String link = Config.API_BASE_URL + "/offer/" + offerId;

		GetRequest request = new GetRequest(link) {
			@Override
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon = new JSONObject(response);
						tangleId = jSon.getInt("tangleId");
						JSONObject offerInformation = jSon
								.getJSONObject("offerInformation");
						viewOfferInfo(offerInformation);
						viewComments(jSon.getJSONArray("comments"));
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
					Toast toast = Toast
							.makeText(
									getApplicationContext(),
									this.getErrorMessage() + " "
											+ this.getStatusCode(),
									Toast.LENGTH_SHORT);
					toast.show();
				}
			}
		};
		request.addHeader("X-SESSION-ID", this.sessionId);
		request.execute();
	}

	/**
	 * Retrieves the required offer information from the JSonObject Views the
	 * offer information
	 * 
	 * @param JSonObject
	 *            offerInformation
	 * @author Almgohar
	 */
	private void viewOfferInfo(JSONObject offerInformation) {
		try {
			viewProfilePicture(offerInformation.getString("offererAvatar"));
			offerDescription.setText(offerInformation
					.getString("offerDescription"));
			offerDeadline.setText(offerInformation.getString("offerDeadline"));
			offererName.setText(offerInformation.getString("offererName"));
			offerDate.setText(offerInformation.getString("offerDate"));
			offerPrice.setText(Integer.toString(offerInformation
					.getInt("offerPrice")));
			final int offererId = offerInformation.getInt("offererId");
			final int requesterId = offerInformation.getInt("requesterId");
			int status = offerInformation.getInt("offerStatus");
			if (status == 0) {
				offerStatus.setText("Pending");
				offerStatus.setTextColor(getResources().getColor(R.color.red));
			} else {
				if (status == 1) {
					offerStatus.setText("Done");
				} else {
					offerStatus.setText("Accepted");
					offerStatus.setTextColor(getResources().getColor(
							R.color.green));
				}
			}

			if (requesterId == loggedInId) {
				validate();
			}

			offererName.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					goToProfile(offererId);
				}
			});
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * Renders the comments in the layout
	 * 
	 * @param comments
	 *            the JSON array of comments
	 * @author mohamedbassem
	 */
	private void viewComments(JSONArray comments) {
		LinearLayout commentsArea = ((LinearLayout) findViewById(R.id.offer_comments_area));
		commentsArea.removeAllViews();
		if (comments.length() > 0) {
			commentsArea.setVisibility(View.VISIBLE);
		}

		for (int i = 0; i < comments.length(); i++) {
			try {
				JSONObject comment = comments.getJSONObject(i);
				CommentEntryFragment entry = new CommentEntryFragment();
				entry.setComment(comment.getString("comment"));
				entry.setCommenter(comment.getString("commenter"));
				entry.setCommentDate(comment.getString("commentDate"));
				getSupportFragmentManager().beginTransaction()
						.add(R.id.offer_comments_area, entry).commit();
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}

		scrollView.postDelayed(new Runnable() {

			@Override
			public void run() {
				scrollView.fullScroll(ScrollView.FOCUS_DOWN);
			}
		}, 500);
	}

	/**
	 * Redirects to a user's profile given his id
	 * 
	 * @param int userId
	 * @author Almgohar
	 */
	private void goToProfile(int userId) {
		Intent profile = new Intent(this, ProfileActivity.class);
		profile.putExtra("user id", userId);
		profile.putExtra("tangle id", this.tangleId);
		startActivity(profile);
	}

	/**
	 * Views the user's profile picture
	 * 
	 * @param String
	 *            imageURL
	 * @author Almgohar
	 */
	public void viewProfilePicture(String imageURL) {
		ImageRequest image = new ImageRequest(offererAvatar);
		image.execute(imageURL);
	}

	/**
	 * this checks if offer can be accepted and calls addAcceptButton() if it
	 * can
	 * 
	 * @param none
	 * @return None
	 * @author sak93
	 */
	public void validate() {

		GetRequest request = new GetRequest(Config.API_BASE_URL + "/offer/"
				+ offerId) {

			@Override
			protected void onPostExecute(String response) {

				try {

					if (this.getStatusCode() == 200) {
						JSONObject jsonResponse = new JSONObject(response);
						JSONObject offerDetails = (JSONObject) jsonResponse
								.get("offerInformation");
						int requestStatus = (Integer) offerDetails
								.get("requestStatus");
						if (requestStatus != 0) {

							return;
						} else {
							int offerStatus = (Integer) offerDetails
									.get("offerStatus");
							if (offerStatus == 0) {
								addAcceptButton();

							}
						}
					} else {
						Toast toast = Toast.makeText(getApplicationContext(),
								getString(R.string.toastError),
								Toast.LENGTH_SHORT);
						toast.show();
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}

			}
		};
		request.addHeader(Config.API_SESSION_ID, sessionId);
		request.execute();
	}

	/**
	 * this adds a button which if clicked sends a POST method to update the
	 * offer as accepted
	 * 
	 * @param none
	 * @return None
	 * @author sak93
	 */
	public void addAcceptButton() throws JSONException {
		acceptOffer.setText("Accept");
		acceptOffer.setVisibility(1);
		returnedResponse = new JSONObject();
		returnedResponse.put("offerId", "" + offerId);
		acceptOffer.setOnClickListener(new View.OnClickListener() {
			int status;

			public void onClick(View v) {
				PostRequest request = new PostRequest(Config.API_BASE_URL
						+ ACCEPT) {
					protected void onPostExecute(String response) {
						status = this.getStatusCode();
						if (status == 201) {
							acceptOffer.setVisibility(View.INVISIBLE);
							offerStatus.setText("Accepted");
							offerStatus.setTextColor(getResources().getColor(
									R.color.green));

						} else {
							if (status == 405) {
								Toast toast = Toast
										.makeText(
												getApplicationContext(),
												getString(R.string.balanceInsufficient),
												Toast.LENGTH_SHORT);
								toast.show();
							} else {
								Toast toast = Toast.makeText(
										getApplicationContext(),
										getString(R.string.toastError),
										Toast.LENGTH_SHORT);
								toast.show();
							}
						}
					}
				};
				request.setBody(returnedResponse);
				settings = getSharedPreferences(Config.SETTING, 0);
				String sessionId = settings.getString(Config.SESSION_ID, "");
				request.addHeader(Config.API_SESSION_ID, sessionId);
				request.execute();

			}

		});

	}

	/**
	 * The callback for the add comment button which adds the comment and
	 * re-renders the layout
	 * 
	 * @param view
	 * @author mohamedbassem
	 */
	public void addComment(View view) {

		PostRequest request = new PostRequest(Config.API_BASE_URL + "/offer/"
				+ offerId + "/comment") {

			@Override
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 201) {
					comment.setText("");
					viewOffer();
				} else {
					Toast.makeText(getApplicationContext(),
							this.getErrorMessage(), Toast.LENGTH_LONG).show();
				}
			}

		};

		String commentMessage = comment.getText().toString();
		if (commentMessage.equals("")) {
			return;
		}
		JSONObject body = new JSONObject();
		try {
			body.put("body", commentMessage);
		} catch (JSONException e) {
			e.printStackTrace();
		}

		request.addHeader(Config.API_SESSION_ID, sessionId);
		request.setBody(body);
		request.execute();
	}

	public void changePrice(View view) {
		final AlertDialog changePriceDialog = new AlertDialog.Builder(this)
				.create();
		changePriceDialog.setCancelable(false);
		changePriceDialog.setMessage("Enter Your New Price!");
		EditText newPrice = new EditText(this);
		newPrice.setHint("Price");
		newPrice.setId(R.id.offerNewPrice);
		newPrice.setSingleLine(true);
		newPrice.setInputType(InputType.TYPE_CLASS_NUMBER);
		changePriceDialog.setView(newPrice);
		changePriceDialog.setButton(BUTTON_POSITIVE, "OK",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						newPriceText = ((EditText) changePriceDialog
								.findViewById(R.id.offerNewPrice)).getText()
								.toString();
						changePrice();
						dialog.dismiss();
					}
				});
		changePriceDialog.show();
	}

	public void changePrice() {
		if (newPriceText.equals("")) {
			Toast.makeText(getApplicationContext(), "Nothing Changed",
					Toast.LENGTH_LONG).show();
		} else {
			if (sessionId == "") {
				Toast.makeText(getApplicationContext(),
						"Session Expired, Please Relogin", Toast.LENGTH_LONG)
						.show();
			} else {
				if (offerId == -1) {
					Toast.makeText(getApplicationContext(),
							"Invalid Offer, Try Again Later", Toast.LENGTH_LONG)
							.show();
				} else {
					sendPriceToServer();
				}
			}
		}
	}

	public void sendPriceToServer() {
		PostRequest imagePostRequest = new PostRequest(Config.API_BASE_URL
				+ "/offers/" + offerId + "/changePrice") {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 403) {
					Toast.makeText(getApplicationContext(),
							"Sorry, You Can't Change The Price Of This Offer",
							Toast.LENGTH_LONG).show();
				} else {
					if (this.getStatusCode() == 409) {
						Toast.makeText(getApplicationContext(),
								"Same Price, Choose a New One",
								Toast.LENGTH_LONG).show();
					} else {
						if (!(this.getStatusCode() == 200)) {
							Toast.makeText(getApplicationContext(),
									"Error, Try Again Later", Toast.LENGTH_LONG)
									.show();
						} else {
							Toast.makeText(getApplicationContext(),
									"Offer Price Changed", Toast.LENGTH_LONG)
									.show();
							TextView originalPrice = (TextView) findViewById(R.id.offer_price);
							originalPrice.setText(newPriceText);
						}
					}
				}
			}
		};
		JSONObject priceJSON = new JSONObject();
		try {
			priceJSON.put("newPrice", Integer.parseInt(newPriceText));
		} catch (JSONException e) {
			e.printStackTrace();
		}
		imagePostRequest.setBody(priceJSON);
		imagePostRequest.addHeader(Config.API_SESSION_ID, sessionId);
		imagePostRequest.execute();
	}

}