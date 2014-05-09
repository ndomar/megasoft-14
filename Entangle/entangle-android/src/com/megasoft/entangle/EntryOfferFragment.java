package com.megasoft.entangle;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class EntryOfferFragment extends Fragment {
	/**
	 * This is the id of the offer
	 */
	private int offerId;

	/**
	 * This is the view of the fragment
	 */
	private View view;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater
				.inflate(R.layout.template_offer_entry, container, false);
		setAttributes();
		view.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity(), OfferActivity.class);
				intent.putExtra("offerID", getOfferId());
				startActivity(intent);
			}
		});
		return view;
	}

	/**
	 * This method id used to set the fields of the view
	 * 
	 * @author HebaAamer
	 */
	private void setAttributes() {
		Bundle args = getArguments();
		setOfferId(args.getInt("offerId"));
		((TextView) view.findViewById(R.id.offerPrice)).setText(args
				.getString("requestedPrice"));
		((TextView) view.findViewById(R.id.offerDescription)).setText(args
				.getString("description"));
		((TextView) view.findViewById(R.id.offererName)).setText(args
				.getString("offerer"));
	}

	/**
	 * This a getter method to get the id of the offer
	 * 
	 * @return offerID
	 * @author HebaAamer
	 */
	public int getOfferId() {
		return offerId;
	}

	/**
	 * This is a setter method to set the id of the offer
	 * 
	 * @param id
	 *            , id of the offer
	 * @author HebaAamer
	 */
	public void setOfferId(int id) {
		this.offerId = id;
	}
}
